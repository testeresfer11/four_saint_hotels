<?php

namespace App\Services\API;

use App\Models\{Hotel, HotelRoomType, HotelRatePlan, HotelRatePlanRoomType};
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SabeeHotelService
{
    public function fetchHotelInventory()
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . '/hotel/inventory');

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch hotel inventory: ' . $response->body());
        }

        return $response->json('data.hotels');
    }

    public function fetchAndStoreHotels()
    {
        $hotels = $this->fetchHotelInventory();

        foreach ($hotels as $hotel) {
            $hotelModel = Hotel::updateOrCreate(
                ['hotel_id' => $hotel['hotel_id']],
                [
                    'name' => $hotel['name'],
                    'city' => $hotel['city'],
                    'country' => $hotel['country'],
                    'zip' => $hotel['zip'],
                    'address' => $hotel['address'],
                    'latitude' => $hotel['latitude'],
                    'longitude' => $hotel['longitude'],
                    'phone' => $hotel['phone'],
                    'email' => $hotel['email'],
                    'currency' => $hotel['currency'],
                ]
            );

            // Fetch and save room types
            $roomTypes = $this->fetchRoomTypesByHotelId($hotel['hotel_id']);

            foreach ($roomTypes as $roomType) {
                $roomTypeModel = HotelRoomType::updateOrCreate(
                    ['room_id' => $roomType['room_type_id']],
                    [
                        'hotel_id' => $hotel['hotel_id'],
                        'room_name' => $roomType['room_type_name'],
                        'property_type' => $roomType['property_type'],
                        'max_occupancy' => $roomType['max_occupancy'],
                        'number_of_rooms' => count($roomType['rooms']),
                        'create_date_time' => now(),
                    ]
                );

                // Save individual rooms inside the room type
                foreach ($roomType['rooms'] as $room) {
                    \App\Models\HotelRoom::updateOrCreate(
                        ['room_id' => $room['room_id']],
                        [
                            'room_type_id' => $roomType['room_type_id'],
                            'hotel_id' => $hotel['hotel_id'],
                            'room_name' => $room['room_name'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            // Save rate plans if available
            if (isset($hotel['rateplans'])) {
                foreach ($hotel['rateplans'] as $plan) {
                    $ratePlanModel = HotelRatePlan::updateOrCreate(
                        ['rateplan_id' => $plan['rateplan_id'], 'hotel_id' => $hotel['hotel_id']],
                        [
                            'rateplan_name'         => $plan['rateplan_name'],
                            'linked_to_master'      => $plan['linked_to_master'],
                            'linked_to_rateplan_id' => $plan['linked_to_rateplan_id'],
                            'price_model'           => $plan['price_model'],
                            'dynamic_pricing'       => $plan['dynamic_pricing'],
                        ]
                    );

                    // Save associated room types if they exist
                    if (!empty($plan['room_types'])) {
                        foreach ($plan['room_types'] as $roomTypeRelation) {
                            HotelRatePlanRoomType::updateOrCreate(
                                [
                                    'rateplan_id' => $plan['rateplan_id'],
                                    'room_type_id' => $roomTypeRelation['room_type_id'],
                                ],
                                [
                                    'default_occupancy' => $roomTypeRelation['price_relation'][0]['default_occupancy'] ?? null
                                ]
                            );
                        }
                    }
                }
            }
        }

        return $hotels;
    }

    public function fetchRoomTypesByHotelId($hotelId)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . "/roomtype/list", [
            'hotel_id' => $hotelId
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch room types: ' . $response->body());
        }

        return $response->json('data.room_types');
    }
}

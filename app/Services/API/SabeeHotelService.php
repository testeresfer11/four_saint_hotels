<?php

namespace App\Services\API;

use App\Models\{Hotel, HotelRoomType, HotelRatePlan, HotelRatePlanRoomType};
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SabeeHotelService
{
    /**
     * Fetch hotel inventory from Sabee API.
     *
     * @return array
     * @throws \Exception
     */
    public function fetchHotelInventory()
    {
        // Send GET request to Sabee hotel inventory endpoint
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . '/hotel/inventory');
           
        // Throw exception if request fails
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch hotel inventory: ' . $response->body());
        }

        // Return hotel data array
        return $response->json('data.hotels');
    }

    /**
     * Fetch hotel inventory and save/update hotels, room types, and rate plans in DB.
     *
     * @return array
     * @throws \Exception
     */
    public function fetchAndStoreHotels()
    {
        $hotels = $this->fetchHotelInventory();
       

        foreach ($hotels as $hotel) {
            // Save or update hotel data
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

            // Fetch and save room types by hotel ID
            $roomTypes = $this->fetchRoomTypesByHotelId($hotel['hotel_id']);

            foreach ($roomTypes as $roomType) {
                // Save or update room type
                $roomTypeModel = HotelRoomType::updateOrCreate(
                    ['room_type_id' => $roomType['room_type_id']],
                    [
                        'hotel_id' => $hotel['hotel_id'],
                        'room_name' => $roomType['room_type_name'],
                        'property_type' => $roomType['property_type'],
                        'max_occupancy' => $roomType['max_occupancy'],
                        'number_of_rooms' => count($roomType['rooms']),
                        'create_date_time' => now(),
                    ]
                );

                // Save individual rooms under this room type
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
                    // Save or update rate plan
                    $ratePlanModel = HotelRatePlan::updateOrCreate(
                        ['rateplan_id' => $plan['rateplan_id'], 'hotel_id' => $hotel['hotel_id']],
                        [
                            'rateplan_name'         => $plan['rateplan_name'],
                           
                            
                        
                        ]
                    );

                    // Save related room types for rate plan
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

    /**
     * Fetch room types by hotel ID from Sabee API.
     *
     * @param int|string $hotelId
     * @return array
     * @throws \Exception
     */
    public function fetchRoomTypesByHotelId($hotelId)
    {
        // Send GET request to fetch room types
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . "/roomtype/list", [
            'hotel_id' => $hotelId
        ]);

        // Throw exception on failure
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch room types: ' . $response->body());
        }

        // Return room types array
        return $response->json('data.room_types');
    }

    /**
     * Get hotel details from local database with relations (room types and rate plans).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hotelDetail($id)
    {
        $hotel = Hotel::with('roomTypes', 'ratePlans','hotelImages')->where('hotel_id', $id)->first();

        if ($hotel) {
            return response()->json([
                'status' => 'success',
                'message' => 'Hotel details fetched successfully.',
                'data' => $hotel
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Hotel not found.',
            ], 404);
        }
    }
}

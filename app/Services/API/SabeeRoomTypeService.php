<?php

namespace App\Services\API;

use App\Models\{Hotel, HotelRoomType, HotelRatePlan, HotelRatePlanRoomType};
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SabeeRoomTypeService
{
    

    /**
     * Fetch hotel inventory and save/update hotels, room types, and rate plans in DB.
     *
     * @return array
     * @throws \Exception
     */
    public function fetchAndStoreRoomTypes($hotelId)
    {
        
            // Fetch and save room types by hotel ID
            $roomTypes = $this->fetchRoomTypesByHotelId($hotelId);

            //dd($roomTypes);

            foreach ($roomTypes as $roomType) {
                // Save or update room type
                $roomTypeModel = HotelRoomType::updateOrCreate(
                    ['room_type_id' => $roomType['room_type_id']],
                    [
                        'hotel_id' => $hotelId,
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
                            'hotel_id' => $hotelId,
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
                        ['rateplan_id' => $plan['rateplan_id'], 'hotel_id' => $hotelId],
                        [
                            'rateplan_name' => $plan['rateplan_name'],
                           
                            
                        
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
        

        return $roomTypes;
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
     * Fetch room rates by hotel ID from Sabee API.
     *
     * @param int|string $hotelId
     * @return array
     * @throws \Exception
     */

    public function fetchRoomRates($hotelId, $startDate, $endDate, $rooms){
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
            'Content-Type' => 'application/json',
        ])->post(config('services.sabee.api_url') . '/connect/availabilityandrates/rate', [
            'hotel_id'   => $hotelId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'rooms'      => $rooms,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch room rates: ' . $response->body());
        }

        return $response->json();
    }


    
}

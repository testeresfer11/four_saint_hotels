<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan,Service};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator};
use App\Services\API\SabeeHotelService;


class SabeeHotelController extends Controller
{
    use SendResponseTrait;
    protected $sabeeHotelService;

    public function __construct(sabeeHotelService $sabeeHotelService)
    {
        $this->sabeeHotelService = $sabeeHotelService;
    }
    /**
     * functionName : index
     * createdDate  : 23-04-2025
     * purpose      : Get hotel data from sabee and save in our db
     */


    public function fetchAndStore(SabeeHotelService $sabeeHotelService)
    {
        try {
            $hotels = $sabeeHotelService->fetchHotelInventory();
        

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

                foreach ($hotel['room_types'] as $room) {
                    HotelRoomType::updateOrCreate(
                        ['room_id' => $room['room_id']],
                        [
                            'hotel_id' => $hotel['hotel_id'],
                            'room_name' => $room['room_name'],
                            'property_type' => $room['property_type'],
                            'max_occupancy' => $room['max_occupancy'],
                            'number_of_rooms' => $room['number_of_rooms'],
                            'create_date_time' => $room['create_date_time'],
                        ]
                    );
                }

                foreach ($hotel['rateplans'] as $plan) {
                    HotelRatePlan::updateOrCreate(
                        ['rateplan_id' => $plan['rateplan_id'], 'hotel_id' => $hotel['hotel_id']],
                        ['rateplan_name' => $plan['rateplan_name']]
                    );
                }
            }

            return $this->apiResponse('success', 200, 'Hotel ' . config('constants.SUCCESS.FETCH_DONE'), ['hotels' => $hotels]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
}

<?php 
namespace App\Services\API;

use Spatie\Permission\Models\Role;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan,Service};

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;


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

        return $hotels;
    }

    

}
?>
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, RoomRate};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator};
use App\Services\API\SabeeHotelService;
use App\Services\API\SabeeRoomTypeService;
use Illuminate\Support\Facades\Http;


class SabeeHotelController extends Controller
{
    use SendResponseTrait;
    protected $sabeeHotelService;
    protected $sabeeRoomTypeService;

    public function __construct(sabeeHotelService $sabeeHotelService, sabeeRoomTypeService $sabeeRoomTypeService)
    {
        $this->sabeeHotelService = $sabeeHotelService;
        $this->sabeeRoomTypeService = $sabeeRoomTypeService;
    }
    /**
     * functionName : fetchAndStore
     * createdDate  : 23-04-2025
     * purpose      : Get hotel data from sabee and save in our db
     * /**
     * Retrieve and store the hotel from sabee app.
     *
     * This method accepts a request, processes it through the service layer, 
     * and returns a JSON response containing the details of the requested page.
     * It handles exceptions gracefully and returns an appropriate error message in case of failure.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the hotel listing or an error message.
     *
     * @throws \Exception If an error occurs during the retrieval of hotel listing, an exception is thrown 
     * and an error response is returned to the client.
     */


    public function fetchAndStore(SabeeHotelService $sabeeHotelService)
    {
        try {
            $hotels = $sabeeHotelService->fetchAndStoreHotels();
            return $this->apiResponse('success', 200, 'Hotel ' . config('constants.SUCCESS.FETCH_DONE'), ['hotels' => $hotels]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }




    /**
     * functionName : roomFetchAndStore
     * createdDate  : 28-05-2025
     * purpose      : Get Room type data from sabee and save in our db
     * /**
     * Retrieve and store the hotel from sabee app.
     *
     * This method accepts a request, processes it through the service layer, 
     * and returns a JSON response containing the details of the requested page.
     * It handles exceptions gracefully and returns an appropriate error message in case of failure.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the hotel listing or an error message.
     *
     * @throws \Exception If an error occurs during the retrieval of hotel listing, an exception is thrown 
     * and an error response is returned to the client.
     */


    public function roomFetchAndStore(sabeeRoomTypeService $sabeeRoomTypeService)
    {
        try {
            $hotel_id = session('selected_hotel_id', 8618);
            $roomTypes = $sabeeRoomTypeService->fetchAndStoreRoomTypes($hotel_id);


            return $this->apiResponse('success', 200, 'Room types ' . config('constants.SUCCESS.FETCH_DONE'), ['data' => $roomTypes]);
        } catch (\Exception $e) {

            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }









    public function getRoomPrice(sabeeRoomTypeService $sabeeRoomTypeService)
    {
        try {
            $hotel_id = session('selected_hotel_id', 8618);

            $roomTypes = $sabeeRoomTypeService->fetchAndStoreRoomTypes($hotel_id);


            $ratePlans = HotelRatePlan::where('hotel_id', $hotel_id)
                ->get(['rateplan_id'])
                ->map(function ($plan) {
                    return ['rateplan_id' => $plan->rateplan_id];
                })
                ->toArray();

            if (empty($ratePlans)) {
                throw new \Exception('No rate plans found for this hotel.');
            }

            $rooms = [];

            foreach ($roomTypes as $room) {
                $rooms[] = [
                    'room_id' => $room['room_type_id'],
                    'rateplans' => $ratePlans,
                    'room_types' => $roomTypesWithCategories,
                ];
            }

            $startDate = now()->format('Y-m-d');
            $endDate = now()->addDays(7)->format('Y-m-d');

            $ratesData = $this->fetchRoomRates($hotel_id, $startDate, $endDate, $rooms);

            // Save rates to DB
            $this->saveRoomRatesToDB($hotel_id, $ratesData);

            return $this->apiResponse('success', 200, 'Room types and rates fetched and saved successfully', [
                'room_types' => $roomTypes,
                'rates' => $ratesData,
            ]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }



    public function fetchRoomRates($hotelId, $startDate, $endDate, $rooms)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
            'Content-Type' => 'application/json',
        ])->post(config('services.sabee.api_url') . 'availabilityandrates/rate', [
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

    /**
     * Save the rates response into DB.
     */
    protected function saveRoomRatesToDB($hotelId, $ratesData)
    {

        if (empty($ratesData['data']['rooms'])) {
            throw new \Exception('No rate data found in response.');
        }

        foreach ($ratesData['data']['rooms'] as $room) {
            $roomId = $room['room_id'];
            $rateplan = $room['rateplan'];
            $rateplanId = $rateplan['rateplan_id'];
            $startDate = \Carbon\Carbon::parse($room['start_date']);
            $endDate = \Carbon\Carbon::parse($room['end_date']);


            foreach ($rateplan['rates'] as $rate) {

                RoomRate::updateOrCreate(
                    [
                        'hotel_id' => $hotelId,
                        'room_id' => $roomId,
                        'rateplan_id' => $rateplanId,
                        'start_rate_date' => $startDate,
                        'end_rate_date' => $endDate,
                        'number_of_guests' => $rate['number_of_guests'] ?? 1,
                    ],
                    [
                        'price' => $rate['amount'],
                        'currency' => $rate['currency'] ?? 'GBP',
                    ]
                );
            }
        }
    }





    /**
     * functionName : detail
     * createdDate  : 23-04-2025
     * purpose      : Get hotel data from sabee and save in our db
     * 
     * retriveve hotel details from database.
     *
     * This method accepts a request, processes it through the service layer, 
     * and returns a JSON response containing the details of the requested page.
     * It handles exceptions gracefully and returns an appropriate error message in case of failure.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the hotel detail or an error message.
     *
     * @throws \Exception If an error occurs during the retrieval of hotel detail, an exception is thrown 
     * and an error response is returned to the client.
     */


    public function detail($id, SabeeHotelService $sabeeHotelService)
    {
        try {
            $hotel = $sabeeHotelService->hotelDetail($id);
            return $this->apiResponse('success', 200, 'Hotel ' . config('constants.SUCCESS.FETCH_DONE'), ['hotel' => $hotel]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }



    /**
     * functionName : getHotels
     * createdDate  : 12-05-2025
     * purpose      : Fetch hotels from the local database
     */


    public function getHotels(Request $request)
    {
        try {
            $query = Hotel::with(['roomTypes', 'ratePlans', 'hotelImages']);

            // Filter by ID
            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            // Filter by name (partial match)
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }

            // Check if lat & long are provided
            if ($request->filled('lat') && $request->filled('long')) {
                $lat = $request->lat;
                $lng = $request->long;

                // Haversine Formula for distance calculation
                $query->selectRaw(
                    '*, (
                         6371 * acos(
                             cos(radians(?)) * cos(radians(latitude)) *
                             cos(radians(longitude) - radians(?)) +
                             sin(radians(?)) * sin(radians(latitude))
                         )
                     ) AS distance',
                    [$lat, $lng, $lat]
                )->orderBy('distance');
            }

            $hotels = $query->get();

            // Add average_rating attribute per hotel
            $hotels->transform(function ($hotel) {
                $hotel->average_rating = round($hotel->feedbacks->avg('rating'), 2);
                return $hotel;
            });

            return $this->apiResponse('success', 200, 'Hotels fetched successfully', [
                'hotels' => $hotels
            ]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }





    public function getRoomTypeByHotel($hotelId)
    {
        try {
            $today = Carbon::today()->toDateString();

            $hotel = Hotel::where('hotel_id', $hotelId)
                ->with(['roomTypes', 'roomTypes.serviceCategories', 'roomTypes.images', 'roomTypes.rates' => function ($query) use ($today) {
                    $query->select('id', 'room_id', 'number_of_guests', 'price', 'currency')
                        ->whereDate('start_rate_date', '<=', $today)
                        ->whereDate('end_rate_date', '>=', $today)
                        ->where('number_of_guests', 1)
                        ->where('rateplan_id', 0);
                }])
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'message' => 'Rooms fetched successfully.',
                'data' => $hotel->roomTypes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 404);
        }
    }


    public function getRoomDetails($roomId)
    {
        try {
            $room = HotelRoom::with(['roomType.hotel'])->findOrFail($roomId);

            return response()->json([
                'status' => 'success',
                'message' => 'Room details fetched successfully.',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 404);
        }
    }
}

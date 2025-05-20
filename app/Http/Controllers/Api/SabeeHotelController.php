<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom};
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
            $query = Hotel::with(['roomTypes', 'ratePlans', 'hotelImages', 'feedbacks','categories','categories.subCategories']);

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





    public function getRoomsByHotel($hotelId)
    {
        try {
            $hotel = Hotel::where('hotel_id', $hotelId)
                ->with(['roomTypes.rooms']) // eager load room types and rooms
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

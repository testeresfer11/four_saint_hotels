<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, HotelImage,RoomTypeImage,ServiceCategory};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator,DB};
use App\Services\API\SabeeRoomTypeService ;
use Illuminate\Support\Facades\Storage;;

class RoomTypeController extends Controller
{
    use SendResponseTrait;
    protected $sabeeRoomTypeService ;

    public function __construct(sabeeRoomTypeService $sabeeRoomTypeService)
    {
        $this->sabeeRoomTypeService = $sabeeRoomTypeService;
    }
    /**
     * functionName : fetchAndStore
     * createdDate  : 23-04-2025
     * purpose      : Get RoomType data from sabee and save in our db
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

        public function fetchAndStore(sabeeRoomTypeService $sabeeRoomTypeService)
        {
            $hotel_id = session('selected_hotel_id', 8618); // Default to 8618 if not in session

            try {
                $hotels = $sabeeRoomTypeService->fetchAndStoreRoomTypes($hotel_id);
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
                $response = $sabeeHotelService->hotelDetail($id);

                // Extract the actual hotel data
                $data = $response->getData();

                if ($data->status === 'success') {
                    $hotel = $data->data;

                    return view('admin.hotel.view', [
                        'hotel' => $hotel,
                    ]);
                } else {
                    return redirect()->back()->with('error', $data->message ?? 'Hotel not found');
                }
            } catch (\Exception $e) {
                return $this->apiResponse('error', 400, $e->getMessage());
            }
        }




    /**
     * functionName : getHotels
     * createdDate  : 12-05-2025
     * purpose      : Fetch hotels from the local database
     */
public function getList()
{
    try {
        $hotel_id = session('selected_hotel_id', 8618); // Default to 8618 if not in session

        $roomTypes = HotelRoomType::where('hotel_id', $hotel_id)->get();
        $hotel = Hotel::where('hotel_id', $hotel_id)->first();
        $service_categories = [];
        if ($hotel) {
            $service_categories = ServiceCategory::where('hotel_id', $hotel->id)->get();

        }
        

        return view('admin.roomtype.list', [
            'data' => $roomTypes,
            'service_categories' => $service_categories
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching roomtype: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error fetching room types. Please try again later.');
    }
}




    /**
     * functionName : getRoomsByHotel
     * createdDate  : 12-05-2025
     * purpose      : Get all rooms of a hotel by hotel ID, including room types.
     *
     * @param int $hotelId
     * @return \Illuminate\Http\JsonResponse
     */

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




    /**
     * functionName : getRoomDetails
     * createdDate  : 12-05-2025
     * purpose      : Fetch full details of a room including its type and related hotel.
     *
     * @param int $roomId
     * @return \Illuminate\Http\JsonResponse
     *   
     */

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



    /**
     * functionName : uploadImages
     * createdDate  : 20-05-2025
     * purpose      : Upload hotel images and update rate per night.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
public function uploadImages(Request $request)
{
    $request->validate([
        'images' => 'nullable|array',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string|max:200',
        'service_categories' => 'nullable|array',
        'service_categories.*' => 'exists:service_categories,id',
       
    ]);

    try {
        $uploadedImages = DB::transaction(function () use ($request) {
            $paths = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('roomtype/images', 'public');
                    $url = asset('storage/' . $path);

                    RoomTypeImage::create([
                        'room_type_id' => $request->room_type_id,
                        'image_path' => $url,
                    ]);

                    $paths[] = $url;
                }
            }

            // Update description and sync features
            $roomType = HotelRoomType::find($request->room_type_id);
            if ($roomType) {
                $roomType->description = $request->description;
                $roomType->save();

                  $roomType->serviceCategories()->sync($request->service_categories ?? []);

            }

            return $paths;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data processed successfully.',
            'images' => $uploadedImages,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to process request.',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    /**
     * functionName : getHotelImages
     * createdDate  : 20-05-2025
     * purpose      : Fetch all images associated with a specific hotel.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHotelImages($id)
    {

        try {
            $images = RoomTypeImage::where('room_type_id', $id)->get();
            
            return response()->json(['status' => true, 'images' => $images]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error fetching images']);
        }
    }



    /**
     * functionName : deleteHotelImage
     * createdDate  : 20-05-2025
     * purpose      : Delete a specific image from storage and database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteHotelImage($id)
    {
       
        $image = RoomTypeImage::where('id',$id)->first();
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Image not found']);
        }
        $imageUrl = $image->image_url;
        $path = parse_url($imageUrl, PHP_URL_PATH);
        $relativePath = ltrim(str_replace('/storage/', '', $path), '/');
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
        $image->delete();

        return response()->json(['status' => true, 'message' => 'Image deleted successfully']);
    }

}

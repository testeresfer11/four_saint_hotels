<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, HotelImage};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator,DB};
use App\Services\API\SabeeHotelService;
use Illuminate\Support\Facades\Storage;



class HotelController extends Controller
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
            $hotels = Hotel::select('*')->with(['roomTypes', 'ratePlans', 'hotelImages'])->get();

            return view('admin.hotel.list', [
                'data' => $hotels,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching bookings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error fetching bookings. Please try again later.');
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
            'hotel_id'       => 'required|exists:hotels,id',
            'rate_per_night' => 'required',
            'images'         => 'required|array',
            'images.*'       => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            
            $uploadedImages = DB::transaction(function () use ($request) {
                $hotelId = $request->hotel_id;
                $paths   = [];


                foreach ($request->file('images') as $image) {
                    $path = $image->store('hotels/images', 'public');
                    $url  = Storage::url($path);

                    $hotelImage = HotelImage::create([
                        'hotel_id'   => $hotelId,
                        'image_path' => $url,
                    ]);

                    $paths[] = $url;
                }

                Hotel::where('id', $hotelId)->update(['rate_per_night' => $request->rate_per_night]);
                return $paths;
            });

            return response()->json([
                'status'  => true,
                'message' => 'Images uploaded and rate updated successfully.',
                'images'  => $uploadedImages,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Failed to upload images.',
                'error'   => $e->getMessage(),
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
            $images = HotelImage::where('hotel_id', $id)->get(); // image_path contains full URL
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
        $image = HotelImage::find($id);
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

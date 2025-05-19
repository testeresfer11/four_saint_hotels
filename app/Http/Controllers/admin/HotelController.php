<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, HotelImage};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator};
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


    public function uploadImages(Request $request)
    {
        try {
            $request->validate([
                'hotel_id' => 'required|exists:hotels,id',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            foreach ($request->file('images') as $image) {
                $path = $image->store('hotels/images', 'public');
                $fullUrl = Storage::url($path);

                HotelImage::create([
                    'hotel_id' => $request->hotel_id,
                    'image_path' => asset($fullUrl),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Images uploaded successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Image upload failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getHotelImages($id)
    {
        try {
            $images = HotelImage::where('hotel_id', $id)->get(); // image_path contains full URL
            return response()->json(['status' => true, 'images' => $images]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error fetching images']);
        }
    }



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

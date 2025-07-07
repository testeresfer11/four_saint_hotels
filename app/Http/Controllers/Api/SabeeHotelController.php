<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, RoomRate,OtherServiceCategory,HotelCoupon,CouponUsage};
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




public function getHotelRoomTypes(Request $request, $hotelId)
{
    try {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Load hotel with all necessary relations
        $hotel = Hotel::where('hotel_id', $hotelId)
            ->with([
                'roomTypes',
                'roomTypes.serviceCategories',
                'roomTypes.images',
                'roomTypes.rates' => function ($query) {
                    $query->select('id', 'room_id', 'number_of_guests', 'price', 'currency', 'start_rate_date')
                        ->where('number_of_guests', 1)
                        ->where('rateplan_id', 0)
                        ->orderByDesc('start_rate_date')
                        ->limit(1);
                },
                'roomTypes.rooms'
            ])
            ->firstOrFail();

        // If no date filter, return all room types
        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => 'success',
                'message' => 'Room types fetched successfully.',
                'data' => $hotel->roomTypes
            ]);
        }

        // Get Sabee room_type_ids (used as room_id in Sabee)
        $sabeeRoomTypeIds = HotelRoomType::where('hotel_id', $hotelId)
            ->pluck('room_type_id')
            ->filter()
            ->unique()
            ->values();

        $roomsForApi = $sabeeRoomTypeIds->map(fn($id) => ['room_id' => (int)$id])->toArray();

        if (empty($roomsForApi)) {
            return response()->json([
                'status' => 'success',
                'message' => 'No rooms found for the selected hotel.',
                'data' => []
            ]);
        }

        // Call SabeeApp availability API
        $response = Http::withHeaders([
            'api_key' => env('SABEE_API_KEY'),
            'api_version' => env('SABEE_API_VERSION'),
            'Content-Type' => 'application/json'
        ])->post(env('SABEE_API_URL') . 'availabilityandrates/availability', [
            'hotel_id' => (int)$hotelId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rooms' => $roomsForApi
        ]);

        if (!$response->ok()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch room availability.',
                'sabee_response' => $response->body()
            ], 500);
        }
       $data = $response->json(); // full response from Sabee
      $roomsData = $response->json('data.rooms') ?? [];
     
        $availability = collect($roomsData)
        ->filter(fn($r) => $r['available_rooms'] > 0)
        ->pluck('room_id')
        ->unique()
        ->values();

        

        // Filter local roomTypes with at least one room's sabee_room_id matching availability
        $filteredRoomTypes = $hotel->roomTypes->filter(function ($roomType) use ($availability) {
            return $roomType->rooms->pluck('room_type_id')->intersect($availability)->isNotEmpty();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Available room types fetched successfully.',
            'data' => $filteredRoomTypes->values()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}






    public function getRoomDetails($roomId)
    {
        try {
            $room = HotelRoom::with([
                'roomType.hotel',
                'roomType.hotel.hotelImages',
                'roomType.images',
                'roomType.serviceCategories',
                'roomType.rates' => function ($query) {
                    $query->select('id', 'room_id', 'number_of_guests', 'price', 'currency', 'start_rate_date')
                          ->where('number_of_guests', 1)
                          ->where('rateplan_id', 0)
                          ->orderByDesc('start_rate_date')
                          ->limit(1);
                },
                'otherServiceCategories.serviceCategory' 
            ])->where('room_id', $roomId)->first();

            if (!$room) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hotel room not found.'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Room details fetched successfully.',
                'data' => $room
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }


 public function calculateTotal(Request $request)
{
    $request->validate([
        'hotel_id'           => 'required|integer|exists:hotels,hotel_id',
        'room_type_id'       => 'required|integer|exists:hotel_room_types,room_type_id',
        'guest_count'        => 'required|integer|min:1',
        'nights'             => 'required|integer|min:1',
        'start_date'         => 'required|date',
        'end_date'           => 'required|date|after_or_equal:start_date',
        'addon_service_ids'  => 'array',
        'addon_service_ids.*'=> 'integer|exists:other_service_categories,id',
        'promo_code'         => 'nullable|string|exists:hotel_coupons,coupon_code',
    ]);

    try {
        // 1) Fetch all rateplan_ids for this hotel
        $rateplanIds = HotelRatePlan::where('hotel_id', $request->hotel_id)
            ->pluck('rateplan_id')
            ->unique()
            ->values()
            ->map(fn($id) => ['rateplan_id' => (int)$id])
            ->toArray();

        // 2) Build the SabeeAPI payload
        $payload = [
            'hotel_id'   => (int)$request->hotel_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'rooms'      => [
                [
                    'room_id'   => (int)$request->room_type_id,
                    'rateplans'=> $rateplanIds
                ]
            ]
        ];

        // 3) Call Sabee’s /rate endpoint
        $response = Http::withHeaders([
            'api_key'      => env('SABEE_API_KEY'),
            'api_version'  => env('SABEE_API_VERSION'),
            'Content-Type' => 'application/json',
        ])->post(env('SABEE_API_URL') . 'availabilityandrates/rate', $payload);

        if (! $response->ok()) {
            return response()->json([
                'status'        => 'error',
                'message'       => 'Failed to fetch room rates.',
                'sabee_message' => $response->body(),
            ], 500);
        }

        // 4) Extract the per‑rateplan pricing for our room
        $roomsData = $response->json('data.rooms') ?? [];
        if (empty($roomsData)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No rate data returned by Sabee.',
            ], 404);
        }

        $rateplanObj = $roomsData[0]['rateplan'] ?? null;

        if (!$rateplanObj) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No rateplan or rates found in Sabee response.',
            ], 404);
        }
        
        $rates = $rateplanObj['rates'];  

        $guestCount   = (int) $request->guest_count;
        $selectedRate = collect($rates)
            ->firstWhere('number_of_guests', $guestCount);


        if (! $selectedRate || ! isset($selectedRate['amount'])) {
            return response()->json([
                'status'  => 'error',
                'message' => "No rate found for {$guestCount} guest(s).",
            ], 404);
        }

        $pricePerNight = $selectedRate['amount'];
        $roomTotal    = $pricePerNight * $request->nights;

        // 6) Add‑ons
        $addonTotal = 0;
        if (! empty($request->addon_service_ids)) {
            $addonTotal = OtherServiceCategory::whereIn('id', $request->addon_service_ids)
                ->sum('price');
        }

        // 7) Promo Code
        $totalBeforeDiscount = $roomTotal + $addonTotal;
        $discount = 0;

        if ($request->promo_code) {
            $promo = HotelCoupon::where('coupon_code', $request->promo_code)->first();
            if ($promo) {
                $usageCount = CouponUsage::where('user_id', Auth::id())
                    ->where('coupon_id', $promo->id)
                    ->count();

                if ($promo->available === 'Once' && $usageCount >= 1) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Coupon already used.',
                    ], 403);
                }
                if ($promo->available === 'Limited' && $usageCount >= $promo->limit_count) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Coupon usage limit reached.',
                    ], 403);
                }

                // percentage vs fixed
                $discount = $promo->type === 'percentage'
                    ? ($promo->value / 100) * $totalBeforeDiscount
                    : $promo->value;

                if ($discount > 0) {
                    CouponUsage::create([
                        'user_id'   => Auth::id(),
                        'coupon_id' => $promo->id,
                    ]);
                }
            }
        }

        $finalTotal = $totalBeforeDiscount - $discount;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'price_per_night'   => $pricePerNight,
                'nights'            => $request->nights,
                'room_total'        => round($roomTotal, 2),
                'addon_total'       => round($addonTotal, 2),
                'promo_discount'    => round($discount, 2),
                'total_price'       => round($finalTotal, 2),
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}




    public function getHotelCoupons($hotelId)
{
    try {
        $userId = auth()->id(); // or pass user ID if not using auth()
        $today = now()->toDateString();

        $coupons = HotelCoupon::where('hotel_id', $hotelId)
            ->whereDate('expiration_date', '>=', $today)
            ->get()
            ->filter(function ($coupon) use ($userId) {
                if ($coupon->available === 'Once') {
                    return !CouponUsage::where('user_id', $userId)->where('coupon_id', $coupon->id)->exists();
                }

                if ($coupon->available === 'Limited') {
                    $usedCount = CouponUsage::where('coupon_id', $coupon->id)->count();
                    return $usedCount < $coupon->max_uses;
                }

                return true; // NotLimited
            })
            ->values(); // Re-index the collection

        return response()->json([
            'status' => 'success',
            'data' => $coupons
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch coupons: ' . $e->getMessage()
        ], 500);
    }
}



}

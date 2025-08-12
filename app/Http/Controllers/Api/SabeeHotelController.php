<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan, Service, HotelRoom, RoomRate,OtherServiceCategory,HotelCoupon,CouponUsage,ServiceCategory,subCategories};
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
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
                'Content-Type' => 'application/json'
            ])->post(config('services.sabee.api_url') . '/availabilityandrates/availability', [
                'hotel_id'   => (int)$hotelId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'rooms'      => $roomsForApi
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
            $room = HotelRoomType::with([
                'hotel',
                'hotel.hotelImages',
                'images',
                'serviceCategories',
                'rates' => function ($query) {
                    $query->select('id', 'room_id', 'number_of_guests', 'price', 'currency', 'start_rate_date')
                          ->where('number_of_guests', 1)
                          ->where('rateplan_id', 0)
                          ->orderByDesc('start_rate_date')
                          ->limit(1);
                },
                'rooms',
                'otherServiceCategories' 
            ])->where('room_type_id', $roomId)->first();

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
        'hotel_id'            => 'required|integer|exists:hotels,hotel_id',
        'room_type_id'        => 'required|integer|exists:hotel_room_types,room_type_id',
        'guest_count'         => 'required|integer|min:1',
        'room_count'          => 'integer|min:1',
        'nights'              => 'required|integer|min:1',
        'start_date'          => 'required|date',
        'end_date'            => 'required|date|after_or_equal:start_date',
        'addon_service_ids'   => 'array',
        'addon_service_ids.*' => 'integer|exists:other_service_categories,id',
        'promo_code'          => 'nullable|string|exists:hotel_coupons,coupon_code',
    ]);

    try {
        $rateplanIds = [['rateplan_id' => 100381]];
        $guestCount = $request->has('room_count') && is_numeric($request->room_count)
            ? (int)$request->guest_count * (int)$request->room_count
            : (int)$request->guest_count;

        // Step 1: Fetch rate
        $rateResponse = Http::withHeaders([
            'api_key'      => 'febfaf24b51e25e5f7a4e0d0f8ca01a5',
            'api_version'  => 1,
            'Content-Type' => 'application/json',
        ])->post('https://api.sabeeapp.com/connect/availabilityandrates/rate', [
            'hotel_id'   => (int)$request->hotel_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'rooms'      => [
                [
                    'room_id'   => (int)$request->room_type_id,
                    'rateplans' => $rateplanIds
                ]
            ]
        ]);

        if (!$rateResponse->ok()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch room rates.',
                'sabee_message' => $rateResponse->body(),
            ], 500);
        }

        $roomsData = $rateResponse->json('data.rooms') ?? [];
        $perDayPrices = [];

            $addedDates = [];

            foreach ($roomsData as $roomBlock) {
                $start = Carbon::parse($roomBlock['start_date']);
                $end = Carbon::parse($roomBlock['end_date']); // Do not subtract here

                $blockRates = $roomBlock['rateplan']['rates'];
                $rateForGuests = collect($blockRates)
                    ->filter(fn($r) => $r['number_of_guests'] <= $guestCount)
                    ->sortByDesc('number_of_guests')
                    ->first();

                if (!$rateForGuests || !isset($rateForGuests['amount'])) continue;

                $amount = $rateForGuests['amount'];

                while ($start->lt(Carbon::parse($request->end_date))) { // ✅ Use request's end_date to limit range
                    $dateStr = $start->toDateString();
                    if (!in_array($dateStr, $addedDates)) {
                        $perDayPrices[] = [
                            'date'  => $dateStr,
                            'price' => $amount
                        ];
                        $addedDates[] = $dateStr;
                    }
                    $start->addDay();
                }
            }



        $rateplanObj = collect($roomsData)
            ->pluck('rateplan')
            ->filter()
            ->firstWhere('rateplan_id', 100381);

        if (!$rateplanObj || empty($rateplanObj['rates'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'No rateplan or rates found in Sabee response.',
            ], 404);
        }

        $rates = $rateplanObj['rates'];
        $rateCollection = collect($rates);
        $bestRate = $rateCollection
            ->filter(fn($rate) => $rate['number_of_guests'] <= $guestCount)
            ->sortByDesc('number_of_guests')
            ->first() ?? $rateCollection->sortByDesc('number_of_guests')->first();

        if (!$bestRate || !isset($bestRate['amount'])) {
            return response()->json([
                'status' => 'error',
                'message' => "No usable rate found for guest count {$guestCount}.",
            ], 404);
        }

        $rateGuests = $bestRate['number_of_guests'];
        $pricePerUnit = $bestRate['amount'];
        $units = ceil($guestCount / $rateGuests);

        // Step 2: Check availability
        $availabilityResponse = Http::withHeaders([
            'api_key'      => 'febfaf24b51e25e5f7a4e0d0f8ca01a5',
            'api_version'  => 1,
            'Content-Type' => 'application/json',
        ])->post('https://api.sabeeapp.com/connect/availabilityandrates/availability', [
            'hotel_id'   => (int)$request->hotel_id,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'rooms'      => [
                ['room_id' => (int)$request->room_type_id]
            ]
        ]);

        if (!$availabilityResponse->ok()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to check room availability.',
                'details' => $availabilityResponse->body()
            ], 500);
        }

        $availabilityData = $availabilityResponse->json('data.rooms');
        $datesWithInsufficientRooms = [];
        foreach ($availabilityData as $day) {
            $available = (int)($day['available_rooms'] ?? 0);
            if ($available < $units) {
                $datesWithInsufficientRooms[] = [
                    'date' => $day['start_date'],
                    'available' => $available,
                    'required' => $units
                ];
            }
        }

        if (!empty($datesWithInsufficientRooms)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not enough rooms available on some dates.',
                'conflicts' => $datesWithInsufficientRooms,
            ], 422);
        }

        // Step 3: Calculate totals
        $roomTotal = $pricePerUnit * $units * $request->nights;
       

        $addonTotal = 0;
        if (!empty($request->addon_service_ids)) {
            $addonTotal = OtherServiceCategory::whereIn('id', $request->addon_service_ids)->sum('price');
        }

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
                        'status' => 'error',
                        'message' => 'Coupon already used.',
                    ], 403);
                }

                if ($promo->available === 'Limited' && $usageCount >= $promo->limit_count) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Coupon usage limit reached.',
                    ], 403);
                }

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
        $subTotal = collect($perDayPrices)->sum('price');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'rate_per_unit'     => $pricePerUnit,
                'unit_guest_count'  => $rateGuests,
                'required_units'    => $units,
                'guest_count'       => $guestCount,
                'nights'            => $request->nights,
                'room_total'        => round($roomTotal, 2),
                'sub_total'         => round($subTotal, 2),
                'addon_total'       => round($addonTotal, 2),
                'promo_discount'    => round($discount, 2),
                'total_price'       => round($finalTotal, 2),
                'raw_rates'         => $rates,
                'room_rates'        => $roomsData,
                'calculated_room_count' => $guestCount,
                'request_room_count'    => $request->room_count ?? 0,
                'per_day_prices'    => $perDayPrices
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
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


     public function getOtheServices(Request $request)
    {
       try {
            $hotels = OtherServiceCategory::where('hotel_id',$request->hotel_id)->get();
            return $this->apiResponse('success', 200, 'Other Service ' . config('constants.SUCCESS.FETCH_DONE'), ['other_services' => $hotels]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }


         public function getFacilities(Request $request)
    {
       try {
            $hotels = ServiceCategory::with('subCategories')->where('hotel_id',$request->hotel_id)->get();
            foreach ($hotels as $image) {
            $image->icon = $image->icon ? asset(ltrim($image->icon, '/')) : null;
        }
            return $this->apiResponse('success', 200, 'Other Service ' . config('constants.SUCCESS.FETCH_DONE'), ['other_services' => $hotels]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }




}

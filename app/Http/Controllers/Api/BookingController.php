<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\API\SabeeBookingService;
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\Http;
use App\Models\Booking;
use App\Models\BookingCustomer;
use App\Models\BookingGuest;
use App\Models\BookingPrice;
use App\Models\BookingService;
use App\Models\BookingServicePrice;
use App\Models\BookingPayment;
use App\Models\OtherServiceCategory;
use Auth;

class BookingController extends Controller
{
    use SendResponseTrait;
    protected $sabeeBookingService;

    public function __construct(SabeeBookingService $sabeeBookingService)
    {
        $this->sabeeBookingService = $sabeeBookingService;
    }


    /**
     * Fetch the list of bookings from SabeeApp for a given hotel and date range.
     *
     * This method retrieves bookings for a specified hotel between the given start and end dates.
     * It also supports optional parameters to extend the list with additional details, services,
     * and guest details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the SabeeBookingService fails to fetch bookings
     */

    public function getBookings(Request $request)
    {

        // Retrieve query parameters
        $hotel_id = session('selected_hotel_id', 8618);
        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');

        if ($start_date) {
            $start_date = $request->query('start_date'); 
        } else {
            $end_date = $request->query('end_date');
        }


        $extended_list = $request->query('extended_list', 1);
        $services = "0"; // Default to 1
        $guest_details = $request->query('guest_details', 1); // Default to 1
        try {

            $bookings = $this->sabeeBookingService->fetchBookings(
                $hotel_id,
                $start_date,
                $end_date,
                $extended_list,
                $services,
                $guest_details
            );

            return $this->apiResponse('success', 200, 'Bookings ' . config('constants.SUCCESS.FETCH_DONE'), ['bookings' => $bookings]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }


   public function getTodayCreatedBookings(Request $request)
    {
        $hotel_id = $request->query('hotel_id', session('selected_hotel_id', 8618));
        $today = now()->format('Y-m-d');

        try {
            $bookings = $this->sabeeBookingService->fetchBookings(
                $hotel_id,
                $today,         // start_date
                $today,         // end_date
                2,              // extended_list = 2 → created/modified today
                0,              // services
                1,              // guest_details
                1               // all_status
            );

            return $this->apiResponse('success', 200, 'Bookings created today fetched successfully.', ['bookings' => $bookings]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }


    /**
     * Create bookings in SabeeApp for a given hotel .
     *
     * This method creates bookings for a specified hotel .
     * It also supports optional parameters to extend the list with additional details, services,
     * and guest details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the SabeeBookingService fails to create bookings
     */

    public function create(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|integer',
            'room_count' => 'required|integer',
            'customer.first_name' => 'required|string',
            'customer.last_name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.phone_number' => 'required|string',
            'customer.country_code' => 'required|string',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_id' => 'required|integer',
            'rooms.*.checkin_date' => 'required|date',
            'rooms.*.checkout_date' => 'required|date',
            'rooms.*.guest_count.adults' => 'required|integer',
            'rooms.*.guest_count.children_ages' => 'nullable|array',
            'rooms.*.prices' => 'required|array',
            'rooms.*.prices.*.date' => 'required|date',
            'rooms.*.prices.*.amount' => 'required|numeric',
            'rooms.*.currency' => 'required|string',
            'rooms.*.total_price' => 'required|numeric',
        ]);

        try {
            // Build payload directly from validated input
            $payload = [
                'hotel_id' => $validated['hotel_id'],
                'room_count' =>$validated['room_count'],
                'reference_id' => uniqid(),
                'customer' => $validated['customer'],
                'rooms' => [],
            ];

           foreach ($validated['rooms'] as $room) {
            $payload['rooms'][] = [
                'checkin_date' => $room['checkin_date'] ?? null,
                'checkout_date' => $room['checkout_date'] ?? null,
                'checkedin_time' => $room['checkedin_time'] ?? null,
                'checkedout_time' => $room['checkedout_time'] ?? null,
                'room_id' => $room['room_id'] ?? null,
                'rateplan_id' => $room['rateplan_id'] ?? 0,
                'name' => $room['name'] ?? '',
                'guests' => $room['guests'] ?? [],
                'guest_count' => $room['guest_count'] ?? [],
                'rate_type' => $room['rate_type'] ?? 'BaseRate',
                'prices' => $room['prices'] ?? [],
                'currency' => $room['currency'] ?? '',
                'total_price' => $room['total_price'] ?? 0,
                'services' => $room['services'] ?? [],
            ];
        }

            $response = $this->sabeeBookingService->createBooking($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking created successfully.',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }




    /**
     * update bookings in SabeeApp for a given hotel .
     *
     * This method update bookings for a specified hotel .
     * It also supports optional parameters to extend the list with additional details, services,
     * and guest details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the SabeeBookingService fails to update bookings
     */

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|integer',
            'reference_id' => 'required|string',
            'status' => 'required|string|in:Confirmed,Cancelled,Pending', // Based on expected status values
            'customer.first_name' => 'required|string',
            'customer.last_name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.phone_number' => 'required|string',
            'customer.country_code' => 'required|string',
            'customer.customer_id' => 'nullable|integer',
            'customer.cc_name' => 'nullable|string',
            'customer.cc_number' => 'nullable|string',
            'customer.cc_type' => 'nullable|string',
            'customer.cc_expiration_date' => 'nullable|string',
            'customer.cc_cvc' => 'nullable|string',
            'customer.token' => 'nullable|string',
            'customer.address' => 'nullable|string',
            'customer.public_space_nature' => 'nullable|string',
            'customer.street_number' => 'nullable|string',
            'customer.building' => 'nullable|string',
            'customer.staircase' => 'nullable|string',
            'customer.floor' => 'nullable|string',
            'customer.door' => 'nullable|string',
            'customer.city' => 'nullable|string',
            'customer.zip' => 'nullable|string',
            'customer.birth_date' => 'nullable|date',
            'customer.citizenship' => 'nullable|string',
            'customer.remarks' => 'nullable|string',

            'rooms' => 'required|array|min:1',
            'rooms.*.reservation_code' => 'required|string',
            'rooms.*.checkin_date' => 'required|date',
            'rooms.*.checkout_date' => 'required|date',
            'rooms.*.checkedin_time' => 'nullable|date_format:Y-m-d H:i:s',
            'rooms.*.checkedout_time' => 'nullable|date_format:Y-m-d H:i:s',
            'rooms.*.room_id' => 'required|integer',
            'rooms.*.rateplan_id' => 'nullable|integer',
            'rooms.*.name' => 'nullable|string',
            'rooms.*.rate_type' => 'nullable|string',
            'rooms.*.currency' => 'required|string',
            'rooms.*.total_price' => 'required|numeric',

            'rooms.*.guest_count.adults' => 'required|integer',
            'rooms.*.guest_count.children_ages' => 'nullable|array',

            'rooms.*.prices' => 'required|array',
            'rooms.*.prices.*.date' => 'required|date',
            'rooms.*.prices.*.amount' => 'required|numeric',

            'rooms.*.guests' => 'nullable|array',
            'rooms.*.guests.*.guest_id' => 'nullable|integer',
            'rooms.*.guests.*.guest_first_name' => 'required|string',
            'rooms.*.guests.*.guest_last_name' => 'required|string',

            'rooms.*.services' => 'nullable|array',
        ]);


        try {
            // Build payload directly from validated input
            $payload = [
                'hotel_id' => $validated['hotel_id'],
                'reference_id' => $validated['reference_id'],
                'status' => $validated['status'],
                'customer' => $validated['customer'],
                'rooms' => [],
            ];

            foreach ($validated['rooms'] as $room) {
                $payload['rooms'][] = [
                    'reservation_code' => $room['reservation_code'],
                    'checkin_date' => $room['checkin_date'],
                    'checkout_date' => $room['checkout_date'],
                    'checkedin_time' => $room['checkedin_time'] ?? null,
                    'checkedout_time' => $room['checkedout_time'] ?? null,
                    'room_id' => $room['room_id'],
                    'rateplan_id' => $room['rateplan_id'] ?? 0,
                    'name' => $room['name'] ?? '',
                    'guests' => $room['guests'] ?? [],
                    'guest_count' => $room['guest_count'],
                    'rate_type' => $room['rate_type'] ?? 'BaseRate',
                    'prices' => $room['prices'],
                    'currency' => $room['currency'],
                    'total_price' => $room['total_price'],
                    'services' => $room['services'] ?? [],
                ];
            }


            $response = $this->sabeeBookingService->updateBooking($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking updated successfully.',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }



    /**
     * cancel bookings in SabeeApp for a given hotel .
     *
     * This method cancel bookings for a specified hotel .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If the SabeeBookingService fails to cancel bookings
     */
    public function cancel(Request $request)
    {
        $reservationCode = $request->reservation_code;
        $hotelId = $request->hotel_id ?? 8618;

        try {
            $response = Http::withHeaders([
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
                'Content-Type' => 'application/json',
            ])->post('https://api.sabeeapp.com/connect/booking/cancel', [
                'hotel_id' => $hotelId,
                'reservation_code' => $reservationCode,
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                // Update local booking status
                $booking = Booking::where('reservation_code', $reservationCode)->first();



                if ($booking) {
                    $booking->status = 'Cancelled';
                    $booking->save();
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Booking cancelled successfully.',
                    'reservation_code' => $reservationCode
                ]);
            } else {
                // Extract and process errors
                $errorMessages = collect($responseData['errors'] ?? [])
                    ->pluck('ret_msg')
                    ->implode('; ');

                // Detect "Already cancelled" scenario
                $alreadyCancelled = str_contains(strtolower($errorMessages), 'already cancelled');

                return response()->json([
                    'status' => $alreadyCancelled ? 'warning' : 'error',
                    'message' => $alreadyCancelled
                        ? 'This booking is already cancelled.'
                        : ($errorMessages ?: 'Unknown error from Sabee API'),
                    'data' => $responseData
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception: ' . $e->getMessage(),
            ]);
        }
    }



    public function checkAvailability(Request $request)
    {


        $validated = $request->validate([
            'hotel_id' => 'required|integer',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'rooms' => 'required|array',
            'rooms.*.room_id' => 'required|integer',
            'rooms.*.guest_count.adults' => 'required|integer|min:1',
            'rooms.*.guest_count.children_ages' => 'nullable|array',
        ]);

        try {
            $response = Http::withHeaders([
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
            ])->post('https://api.sabeeapp.com/connect/booking/availability', $validated);
            return response()->json([
                'status' => 'success',
                'data' => $response->json()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function getOngoingBookings(){
        $user = Auth::user();

        $customer = BookingCustomer::where('email', $user->email)->first();

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'No bookings found for this user.'
            ], 404);
        }

        // Get all booking IDs for this customer
        $bookingIds = BookingCustomer::where('email', $user->email)
            ->pluck('booking_id');

        // Fetch bookings that are ongoing (today is between checkin and checkout)
        $today = now()->toDateString();

        $bookings = Booking::with([
            'customer',
            'bookingGuests',
            'bookingPrices',
            'payments',
            'hotel'
        ])
        ->whereIn('id', $bookingIds)
        ->where('status','!=','CheckedOut')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

    public function getCompleteBookings(){
        $user = Auth::user();

        $customer = BookingCustomer::where('email', $user->email)->first();

        if (! $customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'No bookings found for this user.'
            ], 404);
        }

        // Get all booking IDs for this customer
        $bookingIds = BookingCustomer::where('email', $user->email)
            ->pluck('booking_id');

        // Fetch bookings that are ongoing (today is between checkin and checkout)
        $today = now()->toDateString();

        $bookings = Booking::with([
            'customer',
            'bookingGuests',
            'bookingPrices',
            'payments',
            'hotel'
        ])
        ->whereIn('id', $bookingIds)
        ->whereIn('status',['CheckedOut','Cancelled'])
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $bookings
        ]);
    }

   public function getBookingDetailById($id){

    

        $user = Auth::user();

        $booking = Booking::with([
            'customer',
            'bookingGuests',
            'bookingPrices',
            'payments',
            'hotel',
            'roomType',
            'roomType.images'
        ])->find($id);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found.',
            ], 404);
        }

        // Calculate total price from bookingPrices
        $totalAmount = $booking->bookingPrices->sum('amount');

        // Attach total amount to booking
        $booking->total_price = number_format((float)$totalAmount, 2, '.', '');

        return response()->json([
            'status' => 'success',
            'data' => $booking,
        ]);
    }

    public function saveBookingServices(Request $request, $id)
    {
        try {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json(['message' => 'Booking not found'], 404);
            }

            // services: [{ id: 2, quantity: 3 }, { id: 4, quantity: 1 }]
            $serviceData = collect($request->input('services', []));

            // Get service models
            $serviceIds = $serviceData->pluck('id')->toArray();
            $services = OtherServiceCategory::whereIn('id', $serviceIds)->get()->keyBy('id');

            foreach ($serviceData as $item) {
                $serviceId = $item['id'];
                $quantity = $item['quantity'] ?? 1;

                if (!isset($services[$serviceId])) {
                    continue; 
                }

                $service = $services[$serviceId];
                $totalAmount = $quantity * ($service->price ?? 0);

                // Save booking service
                $bookingService = BookingService::updateOrCreate(
                    [
                        'booking_id' => $booking->id,
                        'service_id' => $service->id,
                    ],
                    [
                        'service_name' => $service->name,
                        'description'  => $service->description,
                        'total_price'  => $totalAmount,
                    ]
                );

                // Save service price
                BookingServicePrice::updateOrCreate(
                    [
                        'booking_service_id' => $bookingService->id,
                        'date'               => now()->toDateString(),
                    ],
                    [
                        'quantity' => $quantity,
                        'vat'      => 0,
                        'city_tax' => 0,
                        'amount'   => $totalAmount,
                    ]
                );
            }

            return response()->json(['message' => 'Services saved successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }






}

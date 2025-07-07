<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\API\SabeeBookingService;
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\Http;


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
            $start_date = $request->query('start_date'); //\Carbon\Carbon::parse($start_date)->startOfMonth()->toDateString();
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
                'reference_id' => uniqid(),
                'customer' => $validated['customer'],
                'rooms' => [],
            ];

            foreach ($validated['rooms'] as $room) {
                $payload['rooms'][] = [
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
        $validated = $request->validate([
            'hotel_id' => 'required|integer',
            'reservation_code' => 'required|string',
        ]);


        try {
            // Build payload directly from validated input
            $payload = [
                'hotel_id' => $validated['hotel_id'],
                'reservation_code' => $validated['reservation_code'],

            ];

            $response = $this->sabeeBookingService->cancelBooking($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Booking cancel successfully.',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
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
}

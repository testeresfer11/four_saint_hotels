<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking, Hotel, HotelRoomType, HotelRoom};
use App\Services\API\SabeeBookingService;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class BookingController extends Controller
{
    use SendResponseTrait;


    protected $sabeeBookingService;

    public function __construct(SabeeBookingService $sabeeBookingService)
    {
        $this->sabeeBookingService = $sabeeBookingService;
    }

    /**
     * 
     * functionName : getList
     * createdDate  : 31-05-2025
     * purpose      : get the cookings
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

   public function getList(Request $request)
{
    $hotel_id = session('selected_hotel_id', 8618);

    // Default date range
    $start_date = $request->query('start_date');
    $start_date = $start_date ? Carbon::parse($start_date)->startOfMonth()->toDateString() : now()->startOfMonth()->toDateString();

    $end_date = $request->query('end_date', now()->toDateString());

    $search = $request->query('search_keyword');
    $status = $request->query('status');
    $checkin_date = $request->query('checkin_date');
    $checkout_date = $request->query('checkout_date');

    try {
        $bookingsQuery = Booking::with([
            'bookingGuests',
            'bookingPrices',
            'bookingServices',
            'customer',
            'payments',
        ])
            ->where('hotel_id', $hotel_id)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                            ->orWhere('email', 'like', "%$search%");
                    })
                    ->orWhereHas('bookingGuests', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                            ->orWhere('email', 'like', "%$search%");
                    })
                    ->orWhere('room_type_name', 'like', "%$search%")
                    ->orWhere('reservation_code', 'like', "%$search%");
                });
            })
            ->when($checkin_date, fn($q) => $q->whereDate('checkin_date', '>=', $checkin_date))
            ->when($checkout_date, fn($q) => $q->whereDate('checkout_date', '<=', $checkout_date));

        $bookings = $bookingsQuery->orderBy('id', 'desc')->paginate(10);

        return view('admin.booking.list', [
            'bookings' => $bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'search_keyword' => $search,
            'status' => $status,
            'selected_hotel_id' => $hotel_id,
            'checkin_date' => $checkin_date,
            'checkout_date' => $checkout_date,
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching bookings: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error fetching bookings. Please try again later.');
    }
}


    /**End method getList**/


    /**
     * 
     * functionName : view
     * createdDate  : 31-05-2024
     * purpose      : View details of a single booking
     *
     * @param  int $id
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view($id)
    {
        try {
            $booking = Booking::with([
                'bookingGuests',
                'bookingPrices',
                'bookingServices',
                'customer',
                'payments',
            ])->findOrFail($id);

            //return $booking->comment;

            return view('admin.booking.view', compact('booking'));
        } catch (\Exception $e) {
            return redirect()->route('admin.booking.list')->with('error', 'Booking not found or error: ' . $e->getMessage());
        }
    }

    /**
     * 
     * functionName : edit
     * createdDate  : 23-05-2025
     * purpose      : edit details of a single booking
     *
     * @param  int $id
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */

    public function edit(Request $request, $id)
    {
        $booking = Booking::with(['bookingGuests', 'bookingPrices', 'bookingServices', 'customer'])->findOrFail($id);

        $guestCount = is_array($booking->guest_count)
            ? $booking->guest_count
            : json_decode($booking->guest_count, true);
        $roomTypes = HotelRoomType::where('hotel_id', $booking->hotel_id ?? 1)->get();
        $rooms = HotelRoom::whereHas('roomType', function ($query) use ($booking) {
            $query->where('hotel_id', $booking->hotel_id ?? 1);
        })->with('roomType')->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'checkin_date' => 'required|date',
                'checkout_date' => 'required|date',
                'status' => 'required|string',
            ]);

            // Update local booking record
            $booking->update([
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'status' => $request->status,
            ]);

            try {
                $payload = [
                    'hotel_id' => $booking->hotel_id,
                    'reference_id' => uniqid(),
                    'status' => $request->status ?? 'Confirmed',
                    'customer' => [
                        'customer_id' => $booking->customer->customer_id,
                        'first_name' => $booking->customer->first_name,
                        'last_name' => $booking->customer->last_name,
                        'email' => $booking->customer->email,
                        'phone_number' => $booking->customer->phone_number,
                        'customer_id' => $booking->customer->customer_id,
                        'country_code' => $booking->customer->country_code ?? 'US',
                    ],
                    'rooms' => [[
                        'reservation_code' => $booking->reservation_code,
                        'checkin_date' => $request->checkin_date,
                        'checkout_date' => $request->checkout_date,
                        'room_id' => $booking->room_id,
                        'guests' => $booking->bookingGuests->map(function ($guest) {
                            return [
                                'guest_id' => $guest->external_id,
                                'guest_first_name' => $guest->first_name,
                                'guest_last_name' => $guest->last_name,
                            ];
                        })->toArray(),
                        'guest_count' => $guestCount,
                        "prices" => [[
                            'date' => Carbon::parse($booking->created_at_api)->toDateString(),

                            "amount" => 0.00
                        ]],
                        'total_price' => $booking->paid,

                        'currency' => $booking->currency,
                    ]],
                ];

                // Conditionally add option_expiry_time if status is Option
                if (($request->status ?? 'Confirmed') === 'Option') {
                    $payload['option_expiry_time'] = $request->option_expiry_time;  // Ensure it's in "YYYY-MM-DD HH:MM:SS" format
                }


                // Send payload to Sabee
                $response = Http::withHeaders([
                    'api_key' => config('services.sabee.api_key'),
                    'api_version' => config('services.sabee.api_version'),
                    'Content-Type' => 'application/json',
                ])->post('https://api.sabeeapp.com/connect/booking/modify', $payload);


                if (!$response->successful()) {
                    throw new \Exception('SabeeApp API error: ' . $response->body());
                }

                return redirect()->route('admin.booking.edit', $id)->with('success', 'Booking updated and synced successfully.');
            } catch (\Exception $e) {
                return $e->getMessage();
                return redirect()->back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
            }
        }

        return view('admin.booking.edit', compact('booking', 'roomTypes', 'rooms'));
    }


    /**
     * 
     * functionName : cancel
     * createdDate  : 23-05-2025
     * purpose      : cancel a single booking
     *
     * @param  int $id
     * @return \Illuminate\View\View
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
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





    /**
     * Get rooms by room type (AJAX endpoint)
     */
    public function getRooms(Request $request)
    {
        $roomTypeId = $request->get('room_type_id');

        if (!$roomTypeId) {
            return response()->json(['rooms' => []]);
        }

        $rooms = HotelRoom::where('room_type_id', $roomTypeId)
            ->select('room_id', 'room_name')
            ->get();

        return response()->json(['rooms' => $rooms]);
    }
}

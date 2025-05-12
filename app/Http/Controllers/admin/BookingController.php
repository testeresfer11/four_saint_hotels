<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking};
use App\Services\API\SabeeBookingService;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
    // Fetch parameters from the request
    $hotel_id = session('selected_hotel_id', null); // Get hotel_id from session or null
    $start_date = $request->query('start_date', now()->subMonth()->startOfMonth()->toDateString());
    $end_date = $request->query('end_date', now()->toDateString());
    $search = $request->query('search_keyword');
    $status = $request->query('status');  // Status filter

    try {
        $bookingsQuery = Booking::with([
            'bookingGuests',
            'bookingPrices',
            'bookingServices',
            'customer'
        ])
        ->whereDate('checkin_date', '>=', $start_date)
        ->whereDate('checkout_date', '<=', $end_date)
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                        ->orWhere('email', 'like', "%$search%");
                })
                ->orWhere('room_type_name', 'like', "%$search%")
                ->orWhere('reservation_code', 'like', "%$search%");
            });
        })
        ->when($status, function ($query) use ($status) {
            $query->where('status', $status);
        });

        // Check if a hotel ID is set in the session
        if ($hotel_id) {
            $bookingsQuery->where('hotel_id', $hotel_id);
        }

        $bookings = $bookingsQuery->orderBy('checkin_date', 'asc')
                                  ->paginate(10);

        return view('admin.booking.list', [
            'bookings' => $bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'search_keyword' => $search,
            'status' => $status,  // Pass status filter to the view
            'selected_hotel_id' => $hotel_id  // Pass selected hotel ID to the view
        ]);
    } catch (\Exception $e) {
        // Log the exception and show a generic error message
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
                'customer'
            ])->findOrFail($id);

           //return $booking->comment;

            return view('admin.booking.view', compact('booking'));
        } catch (\Exception $e) {
            return redirect()->route('admin.booking.list')->with('error', 'Booking not found or error: ' . $e->getMessage());
        }
    }
}

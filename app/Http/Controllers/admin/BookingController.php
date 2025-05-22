<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Booking,Hotel};
use App\Services\API\SabeeBookingService;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
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
        });

        $bookings = $bookingsQuery->orderBy('id', 'desc')->paginate(10);

        return view('admin.booking.list', [
            'bookings' => $bookings,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'search_keyword' => $search,
            'status' => $status,
            'selected_hotel_id' => $hotel_id,
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
}

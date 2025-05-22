<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Payment, Role, User,Booking,BookingGuest,Hotel,BookingPayment};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * functionName : index
     * createdDate  : 29-05-2024
     * purpose      : Get the dashboard detail for the admin
     */
 public function index()
{
    $role = Role::where('name', config('constants.ROLES.USER'))->first();
    $userQuery = User::whereNull('deleted_at')->where('role_id', $role->id);

    $hotelId = session('selected_hotel_id') ?? 8618;

    // Fetch latest bookings
    $bookings       = Booking::with('bookingGuests')->when($hotelId, fn($q) => $q->where('hotel_id', $hotelId))->latest()->limit(5)->get();

   
    $bookingBase    = Booking::when($hotelId, fn($q) => $q->where('hotel_id', $hotelId));
    $totalBookings  = (clone $bookingBase)->count();
    $totalConfirmed = (clone $bookingBase)->where('status', 'Confirmed')->count();
    $totalCheckedIn = (clone $bookingBase)->where('status', 'CheckedIn')->count();
    $totalOnboard   = (clone $bookingBase)->where('status', 'Onboard')->count();
    $totalOption    = (clone $bookingBase)->where('status', 'Cancelled')->count();
    $totalCheckedOut= (clone $bookingBase)->where('status', 'CheckedOut')->count();

    $totalGuests  = BookingGuest::count();
    $totalEarning = BookingPayment::sum('amount');

    // Generate dynamic months & earnings (Jan–Dec example)
    $paymentsByMonth = DB::table('booking_payments')
        ->selectRaw("MONTH(payment_date) as month_number, DATE_FORMAT(payment_date, '%b') as month_label, SUM(amount) as total")
        ->whereYear('payment_date', now()->year)
        ->groupBy('month_number', 'month_label')
        ->orderBy('month_number')
        ->get()
        ->keyBy('month_label');

    $allMonths = collect(range(1,12))
        ->map(fn($n) => \Carbon\Carbon::create()->month($n)->format('M'))
        ->toArray();

    $months = $monthlyEarnings = [];
    foreach ($allMonths as $m) {
        $months[]          = $m;
        $monthlyEarnings[] = $paymentsByMonth->has($m) ? (float)$paymentsByMonth[$m]->total : 0;
    }

    $responseData = [
        'total_registered_user' => $userQuery->count(),
        'total_active_user'     => $userQuery->where('status', 1)->count(),
        'total_bookings'        => $totalBookings,
        'total_confirmed'       => $totalConfirmed,
        'total_checkedIn'       => $totalCheckedIn,
        'total_onboared'        => $totalOnboard,
        'total_Option'          => $totalOption,
        'total_checkedOut'      => $totalCheckedOut,
        'total_guests'          => $totalGuests,
        'total_earning'         => $totalEarning,
        'months'                => json_encode($months),
        'monthly_earnings'      => json_encode($monthlyEarnings),
    ];

    return view('admin.dashboard', [
        'responseData' => $responseData,
        'bookings'     => $bookings,
    ]);
}



    /**End method index**/


   // HotelController.php
public function selectHotel(Request $request)
{

    $hotelId = $request->input('hotel_id');


       if ($hotelId) {
        session(['selected_hotel_id' => $hotelId]);
    } else {
        session()->forget('selected_hotel_id');
    }

    return redirect()->back();
}



}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Payment, Role, User,Booking,BookingGuest,Hotel};
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
    $user = User::whereNull('deleted_at')->where('role_id', $role->id);

    $hotelId = session('selected_hotel_id') ?? 8618;

    $totalBookings = Booking::when($hotelId, function ($query) use ($hotelId) {
        return $query->where('hotel_id', $hotelId);
    })->count();

    $totalGuests = BookingGuest::count();

    // Example earnings (you can update this logic based on your actual Transaction model)
    $totalEarning = 200;

    // Dummy data for chart (replace with actual monthly revenue/booking data)
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $monthlyEarnings = [500, 800, 600, 1200, 900, 700];

    $responseData = [
        'total_registered_user' => $user->clone()->count(),
        'total_active_user'     => $user->clone()->where('status', 1)->count(),
        'total_bookings'        => $totalBookings,
        'total_guests'          => $totalGuests,
        'total_earning'         => $totalEarning,
        'months'                => json_encode($months),
        'monthly_earnings'      => json_encode($monthlyEarnings),
    ];

    return view("admin.dashboard", compact('responseData'));
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

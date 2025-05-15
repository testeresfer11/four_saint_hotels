<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Payment, Role, User,Booking,BookingGuest};
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

    $hotelId = session('selected_hotel_id') ?? 8618; // may be null

    // Bookings count
    $totalBookings = Booking::when($hotelId, function ($query) use ($hotelId) {
        return $query->where('hotel_id', $hotelId);
    })->count();

    // Guests count
    $totalGuests = BookingGuest::count();

    $responseData = [
        'total_registered_user' => $user->clone()->count(),
        'total_active_user'     => $user->clone()->where('status', 1)->count(),
        'total_bookings'        => $totalBookings,
        'total_guests'          => $totalGuests,
        'months'                => json_encode([]),
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

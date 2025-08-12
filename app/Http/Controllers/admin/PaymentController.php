<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\BookingPayment;

class PaymentController extends Controller
{
    public function getList(Request $request){
        $hotelId = session('selected_hotel_id', 8618);

        $payments = BookingPayment::with('booking')
            ->whereHas('booking', function ($query) use ($hotelId, $request) {
                $query->where('hotel_id', $hotelId);

                if ($request->filled('search_keyword')) {
                    $query->where('reservation_code', 'like', '%' . $request->search_keyword . '%');
                }

                if ($request->filled('from_date')) {
                    $query->whereDate('checkin_date', '>=', $request->from_date);
                }

                if ($request->filled('to_date')) {
                    $query->whereDate('checkout_date', '<=', $request->to_date);
                }
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view("admin.transaction.list", compact("payments"));
    }

}

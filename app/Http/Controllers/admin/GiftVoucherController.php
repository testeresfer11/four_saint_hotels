<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelCoupon;
use Illuminate\Support\Str;
use App\Traits\SendResponseTrait;
use App\Services\API\SabeeCouponService;

class GiftVoucherController extends Controller
{
    use SendResponseTrait;

    protected $couponService;

    public function __construct(SabeeCouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * functionName : getList
     * createdDate  : 21-04-2025
     * purpose      :Fetch the voucher gift
     * 
     */
    public function index(Request $request)
    {
        $hotelId = session('selected_hotel_id', 8618);
        $coupons = HotelCoupon::where('hotel_id', $hotelId)->get();

        return view('admin.voucher.list', compact('coupons'));
    }

    public function sync(Request $request)
    {
        $hotelId = session('selected_hotel_id', 8618);
        $this->couponService->syncCoupons($hotelId);

        return redirect()->route('admin.vouchers.index')->with('success', 'Coupons synced successfully.');
    }
}
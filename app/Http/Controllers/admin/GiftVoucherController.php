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

    $coupons = HotelCoupon::where('hotel_id', $hotelId)
        ->when($request->filled('search_keyword'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('coupon_code', 'like', "%{$request->search_keyword}%")
                      ->orWhere('coupon_name', 'like', "%{$request->search_keyword}%");
            });
        })
        ->when($request->filled('start_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '>=', $request->start_date);
        })
        ->when($request->filled('end_date'), function ($query) use ($request) {
            $query->whereDate('created_at', '<=', $request->end_date);
        })
        ->orderBy('id', 'desc')
        ->get();

    return view('admin.voucher.list', compact('coupons'));
}


    public function sync(Request $request)
    {
        $hotelId = session('selected_hotel_id', 8618);
        $this->couponService->syncCoupons($hotelId);

        return redirect()->route('admin.vouchers.index')->with('success', 'Coupons synced successfully.');
    }


    public function add(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                // Just return the add coupon form
                return view("admin.voucher.add");
            }

            if ($request->isMethod('post')) {
                // Validate fields
                $validator = \Validator::make($request->all(), [
                    'coupon_code'      => 'required|string|max:255|unique:hotel_coupons,coupon_code',
                    'coupon_name'      => 'required|string|max:255',
                    'type'             => 'required|in:Fixed,Percentage',
                    'value'            => 'required|numeric|min:0',
                    'available'        => 'nullable',
                    'expiration_date'  => 'nullable|date|after_or_equal:today',
                    'max_uses'         => 'nullable|integer|min:1',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Create coupon
                HotelCoupon::create([
                    'hotel_id'        => session('selected_hotel_id', 8618),
                    'coupon_code'     => $request->coupon_code,
                    'coupon_name'     => $request->coupon_name,
                    'type'            => $request->type,
                    'value'           => $request->value,
                    'available'       => $request->available,
                    'expiration_date' => $request->expiration_date,
                    'max_uses'        => $request->max_uses,
                ]);

                return redirect()->route('admin.vouchers.index')->with('success', 'Coupon added successfully!');
            }

        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function edit(Request $request, $id)
{
    try {
        $voucher = HotelCoupon::findOrFail($id);

        if ($request->isMethod('get')) {
            
            return view('admin.voucher.edit', compact('voucher'));
        }

        if ($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), [
                'coupon_code'      => 'required|string|max:255|unique:hotel_coupons,coupon_code,' . $voucher->id,
                'coupon_name'      => 'required|string|max:255',
                'type'             => 'required|in:Fixed,Percentage',
                'value'            => 'required|numeric|min:0',
                'available'        => 'nullable',
                'expiration_date'  => 'nullable|date|after_or_equal:today',
                'max_uses'         => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $voucher->update([
                'hotel_id'        => session('selected_hotel_id', 8618),
                'coupon_code'     => $request->coupon_code,
                'coupon_name'     => $request->coupon_name,
                'type'            => $request->type,
                'value'           => $request->value,
                'available'       => $request->available,
                'expiration_date' => $request->expiration_date,
                'max_uses'        => $request->max_uses,
            ]);

            return redirect()->route('admin.vouchers.index')->with('success', 'Coupon updated successfully!');
        }

    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

public function delete($id)
{
    try {
        $coupon = HotelCoupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Coupon deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}



}
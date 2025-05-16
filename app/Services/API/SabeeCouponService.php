<?php

namespace App\Services\API;

use App\Models\HotelCoupon;
use Illuminate\Support\Facades\Http;

class SabeeCouponService
{
    

  public function syncCoupons($hotelId)
{
    $response = Http::withHeaders([
        'api_key' => config('services.sabee.api_key'),
        'api_version' => config('services.sabee.api_version'),
    ])->get(config('services.sabee.api_url') . '/coupon/list', [
        'hotel_id' => $hotelId,
    ]);

    if ($response->successful()) {
        $coupons = $response->json('data.coupons');

        if (!is_array($coupons)) {
            \Log::error('Coupons data is not an array: ', $response->json());
            return false;
        }

        foreach ($coupons as $coupon) {
            HotelCoupon::updateOrCreate(
                ['coupon_code' => $coupon['coupon_code']],
                [
                    'hotel_id'        => $hotelId,
                    'coupon_name'     => $coupon['coupon_name'],
                    'type'            => $coupon['type'],
                    'value'           => $coupon['value'],
                    'available'       => $coupon['available'],
                    'expiration_date' => $coupon['expiration_date'] ?? null,
                ]
            );
        }

        return true;
    }

    \Log::error('Failed to fetch coupons from Sabee API: ' . $response->body());
    return false;
}

}
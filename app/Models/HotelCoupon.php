<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelCoupon extends Model
{
    protected $fillable = [
        'hotel_id', 'coupon_code', 'coupon_name', 'type', 'value', 'available', 'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
     protected $fillable = ['user_id', 'coupon_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(HotelCoupon::class, 'coupon_id');
    }
}

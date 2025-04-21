<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'room_type',
        'rate_plan',
        'check_in_date',
        'check_out_date',
        'status',
        'total_price',
        'payment_status',
    ];
}

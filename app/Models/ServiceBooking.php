<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    protected $fillable = [
        'service_id',
        'booking_id',
        'guest_name',
        'service_date',
        'status',
        'price',
    ];
}

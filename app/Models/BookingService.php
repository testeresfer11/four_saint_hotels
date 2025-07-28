<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    protected $fillable = [
        'booking_id', 'service_id', 'service_name', 'category_name', 'description', 'included',
        'compulsory', 'price_type', 'price_applicable', 'billing_type', 'unit', 'total_price','start_date','end_date'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingServicePrices()
    {
        return $this->hasMany(BookingServicePrice::class);
    }
}

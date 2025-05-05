<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingServicePrice extends Model
{
    protected $fillable = [
        'booking_service_id', 'date', 'quantity', 'vat', 'city_tax', 'amount'
    ];

    // Define the relationship to the Booking model (parent model)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Define the relationship to the BookingService model (parent model)
    public function bookingService()
    {
        return $this->belongsTo(BookingService::class);
    }
}

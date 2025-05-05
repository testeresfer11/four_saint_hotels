<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPrice extends Model
{
    protected $fillable = [
        'booking_id', 'date', 'vat', 'city_tax', 'amount'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

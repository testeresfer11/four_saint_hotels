<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingGuest extends Model
{
    protected $fillable = [
        'booking_id', 'first_name', 'last_name', 'birth_date', 'birth_place', 'citizenship',
        'country_code', 'email', 'phone_number', 'passport_number', 'remarks'
    ];
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

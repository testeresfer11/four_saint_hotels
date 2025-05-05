<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCustomer extends Model
{
    protected $fillable = [
        'booking_id', 'first_name', 'last_name', 'birth_date', 'citizenship', 'address',
        'city', 'zip', 'country_code', 'phone_number', 'remarks', 'email'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reservation_code',
        'group_id',
        'door_code',
        'channel_id',
        'license_plate',
        'hotel_id',
        'checkin_date',
        'checkout_date',
        'status',
        'room_type_id',
        'room_type_name',
        'room_id',
        'room_name',
        'number_of_guests',
        'guest_count',
        'room_price',
        'paid',
        'currency',
        'rateplan',
        'comment',
        'created_at_api',
        'updated_at_api'
    ];

    protected $casts = [
        'license_plate' => 'array',
        'guest_count' => 'array',
        'rateplan' => 'array'
    ];

    public function bookingGuests()
    {
        return $this->hasMany(BookingGuest::class);
    }

    public function bookingPrices()
    {
        return $this->hasMany(BookingPrice::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function customer()
    {
        return $this->hasOne(BookingCustomer::class);
    }
    public function bookingServicePrices()
{
    return $this->hasMany(BookingServicePrice::class);
}
}

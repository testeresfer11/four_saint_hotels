<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'booking_id', 'payment_type', 'amount', 'currency', 'payment_date', 'payment_status'
    ];



    public function booking(){
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}

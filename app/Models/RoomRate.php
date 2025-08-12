<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRate extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_id',
        'rateplan_id',
        'price',
        'number_of_guests',
        'currency',
        'start_rate_date',
        'end_rate_date',
    ];

    public function hotelRoomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id', 'room_id');

    }
}

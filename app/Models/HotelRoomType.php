<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_id',
        'room_name',
        'property_type',
        'max_occupancy',
        'number_of_rooms',
        'create_date_time',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }
}

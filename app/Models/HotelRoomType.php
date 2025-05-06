<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
{
    protected $guarded = [];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class, 'room_type_id', 'room_type_id');
    }
}

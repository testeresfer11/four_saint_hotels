<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAvailability extends Model
{
   
    protected $fillable = ['room_type_id', 'start_date', 'end_date', 'available_rooms'];


    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

}

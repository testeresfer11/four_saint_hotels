<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $guarded = [];

    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id', 'room_type_id');
    }
    
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    public function otherServiceCategories(){
     return $this->hasMany(HotelRoomOtherServiceCategory::class, 'hotel_room_id','room_id');
    }
}

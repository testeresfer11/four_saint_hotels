<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoomOtherServiceCategory extends Model
{
   protected $table = 'hotel_room_other_service_category';

    protected $fillable = ['hotel_room_id', 'other_service_category_id'];


       public function hotelRoomtype()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id');
    }

   
    public function serviceCategory()
    {
        return $this->belongsTo(OtherServiceCategory::class, 'other_service_category_id');
    }
}

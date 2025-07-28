<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherServiceCategory extends Model
{
     protected $fillable = ['name', 'description', 'price', 'is_active','hotel_id','icon','hotel_room_type_id','total_quantity','total_left'];

  public function hotelRoomType()
{
    return $this->belongsTo(HotelRoomType::class, 'hotel_room_type_id', 'room_type_id');
}

}

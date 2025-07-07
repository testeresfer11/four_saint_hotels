<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoomType extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'room_type_id';

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }

    public function rooms()
    {
        return $this->hasMany(HotelRoom::class, 'room_type_id', 'room_type_id');
    }
    public function rates()
    {
        return $this->hasMany(RoomRate::class, 'room_id', 'room_type_id');
    }

    public function images(){
        return $this->hasMany(RoomTypeImage::class, 'room_type_id', 'room_type_id');
    }

   
    public function serviceCategories(){
        return $this->belongsToMany(
          ServiceCategory::class,
          'hotel_room_type_service_category',
          'hotel_room_type_id',
          'service_category_id'
        );
    }

    public function availabilities()
    {
        return $this->hasMany(RoomAvailability::class, 'room_type_id');
    }
}

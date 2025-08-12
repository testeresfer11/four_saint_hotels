<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomTypeImage extends Model
{
    protected $fillable = [
        'room_type_id',
        'image_path',
    ];

    // Relationship: Each image belongs to a RoomType
    public function roomType()
    {
        return $this->belongsTo(HotelRoomType::class, 'room_type_id', 'room_type_id');
    }
}

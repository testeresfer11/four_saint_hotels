<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRoom extends Model
{
    protected $guarded = [];

    public function roomType()
    {
        return $this->belongsTo(Hotel::class, 'room_type_id', 'room_type_id');
    }
}

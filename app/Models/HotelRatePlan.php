<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRatePlan extends Model
{
    protected $guarded = [];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }
}

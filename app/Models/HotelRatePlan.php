<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelRatePlan extends Model
{
    protected $fillable = [
        'hotel_id',
        'rateplan_id',
        'rateplan_name',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'hotel_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelImage extends Model
{
    protected $fillable = [
        'hotel_id',
        'image_path',
    ];

   
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
    protected function imagePath(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url($value) // full URL
        );
    }
}

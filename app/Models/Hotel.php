<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'city',
        'country',
        'zip',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'currency',
        'rate_per_night',
        'description',
    ];

    public function roomTypes()
    {
        return $this->hasMany(HotelRoomType::class, 'hotel_id', 'hotel_id');
    }

     public function categories()
    {
        return $this->hasMany(ServiceCategory::class);
    }


    public function ratePlans()
    {
        return $this->hasMany(HotelRatePlan::class, 'hotel_id', 'hotel_id');
    }
    public function hotelImages()
    {
        return $this->hasMany(HotelImage::class, 'hotel_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'hotel_id', 'hotel_id');
    }
    
    public function averageRating()
    {
        return $this->feedbacks()->avg('rating');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = [
        'hotel_id',
        'title',
        'icon',
        'description',
    ];


    public function subCategories()
    {
        return $this->hasMany(ServiceSubCategory::class, 'category_id');
    }


    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}

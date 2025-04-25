<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'service_id',
        'hotel_id',
        'service_name',
        'service_category_name',
        'description',
        'image_url',
        'included',
        'compulsory',
        'price_type',
        'price_applicable',
        'billing_type',
        'unit',
        'price',
        'vat',
        'apply_city_tax',
        'currency',
        'available_rateplans',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'included' => 'boolean',
        'compulsory' => 'boolean',
        'apply_city_tax' => 'boolean',
        'available_rateplans' => 'array',
    ];

    /**
     * Get the hotel that owns the service.
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
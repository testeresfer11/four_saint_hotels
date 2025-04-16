<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'profile',
        'phone_number',
        'address',
        'zip_code',
        'gender',
        'country_code',
        'dob',
        'country_short_code'
    ];

}

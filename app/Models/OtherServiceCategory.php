<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherServiceCategory extends Model
{
     protected $fillable = ['name', 'description', 'price', 'is_active'];
}

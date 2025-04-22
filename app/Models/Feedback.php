<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = ['user_id','hotel_id','message', 'rating','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

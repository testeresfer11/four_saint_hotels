<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class chat extends Model
{
    protected $fillable = [
        "room_id",
        "from_id",
        "to_id",
        "message"
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'message'];


    
   
}

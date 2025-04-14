<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'phone',
        'title',
        'message',
        'type',
        'notification_type',
        'read_status',
        'read_at',
        'ordering'
    ];
    
}

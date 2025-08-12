<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = [
        'receiver_id',
        'title',
        'body',
        'type',
        'image',
        'notification_type',
    ];

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}

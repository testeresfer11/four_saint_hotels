<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OTPRequest extends Model
{
    protected $table = 'otp_requests';
    
    protected $fillable = [
        'user_id',
        'otp',
        'otp_expiry'
    ];
}

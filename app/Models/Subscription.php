<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'description',
        'android_sku_code',
        'ios_sku_code',
        'status',
        'ordering'
    ];


    public function user()
    {
        return $this->belongsToMany(User::class,'user_subscriptions','subscription_id','user_id')
        ->withPivot('transaction_id');
    }
    
}

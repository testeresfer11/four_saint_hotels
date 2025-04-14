<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = "admins";
    protected $guard = 'admin';

    protected $fillable = [
        'name',
        'gender',
        'email',
        'email_verified_at',
        'is_email_verified',
        'password',
        'remember_token',
        'phone_number',
        'status',
        'profile_pic',
        'google_id',
        'google_email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

}

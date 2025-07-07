<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'voucher_code',
        'amount',
        'expiry_date',
        'status',
        'guest_id',
        'created_by_admin_id',
    ];

    // Relationship with Guest
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

 
    // Relationship with purchases
    public function purchases()
    {
        return $this->hasMany(VoucherPurchase::class);
    }
}

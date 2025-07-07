<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address',
        'profile_picture',
        'status',
    ];

    // Relationship with gift vouchers
    public function giftVouchers()
    {
        return $this->hasMany(GiftVoucher::class);
    }

    // Relationship with voucher purchases
    public function voucherPurchases()
    {
        return $this->hasMany(VoucherPurchase::class);
    }
}


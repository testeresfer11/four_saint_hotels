<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'gift_voucher_id',
        'payment_gateway',
        'transaction_id',
        'paid_amount',
        'payment_status',
    ];

    // Relationship with the Guest who made the purchase
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Relationship with the GiftVoucher purchased
    public function giftVoucher()
    {
        return $this->belongsTo(GiftVoucher::class, 'gift_voucher_id');
    }
}


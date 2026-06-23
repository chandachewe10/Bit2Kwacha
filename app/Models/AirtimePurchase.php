<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirtimePurchase extends Model
{
    protected $fillable = [
        'mobile_number',
        'amount_btc',
        'amount_sats',
        'amount_kwacha',
        'convenience_fee',
        'total_sats',
        'network_fee',
        'qr_code_path',
        'lightning_invoice_address',
        'checking_id',
        'checkout_url',
        'payment_status',
        'paid_at',
        'airtime_request_id',
        'airtime_status',
        'airtime_response',
        'user_id',
    ];

    protected $casts = [
        'airtime_response' => 'array',
        'paid_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'subtotal',
        'tax',
        'shipping',
        'discount_amount',
        'bank_transfer_discount_amount',
        'other_charges',
        'total',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'bank_transfer_discount_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function rewardClaim()
    {
        return $this->hasOne(RewardClaim::class);
    }
}

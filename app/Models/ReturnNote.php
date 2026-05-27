<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'return_request_id',
        'type',
        'note_number',
        'subtotal',
        'tax',
        'discount_amount',
        'adjustment_amount',
        'total',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }

    public function items()
    {
        return $this->hasMany(ReturnNoteItem::class);
    }
}

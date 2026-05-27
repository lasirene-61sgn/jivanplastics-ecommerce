<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_note_id',
        'order_item_id',
        'quantity',
        'pieces',
        'unit_price',
        'tax_amount',
        'discount_amount',
        'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function returnNote()
    {
        return $this->belongsTo(ReturnNote::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_id',
        'order_id',
        'order_item_id',
        'customer_id',
        'reason',
        'description',
        'type',
        'quantity',
        'pieces',
        'status',
        'damage_proof_image',
        'another_image',
        'dispatch_proof_image',
        'invoice_proof_image',
        'admin_notes',
        'resolved_at'
    ];
    
    protected $casts = [
        'resolved_at' => 'datetime',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the system-generated invoice for this return request.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function returnNote()
    {
        return $this->hasOne(ReturnNote::class);
    }
}
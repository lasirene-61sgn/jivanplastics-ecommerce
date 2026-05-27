<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'per_unit_pieces',
        'total_pieces',
        'manufactured_pieces',
        'rejected_pieces',
        'quantity',
        'manufactured_quantity',
        'rejected_quantity',
        'rejection_reason',
        'dispatched_quantity',
        'price',
        'piece_price',
        'original_price',
        'discount_amount',
        'tax',
        'total',
        'size',
        'thickness',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:4',
        'per_unit_pieces' => 'integer',
        'total_pieces' => 'integer',
        'manufactured_pieces' => 'integer',
        'rejected_pieces' => 'integer',
        'manufactured_quantity' => 'decimal:4',
        'rejected_quantity' => 'decimal:4',
        'dispatched_quantity' => 'decimal:4',
        'price' => 'decimal:2',
        'piece_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the pending pieces for manufacturing.
     */
    public function getManufacturingPendingPiecesAttribute()
    {
        return max(0, $this->total_pieces - ($this->manufactured_pieces + $this->rejected_pieces));
    }

    /**
     * Get the pending quantity for manufacturing (Units).
     */
    public function getManufacturingPendingQuantityAttribute()
    {
        return max(0, $this->quantity - ($this->manufactured_quantity + $this->rejected_quantity));
    }

    /**
     * Get the dispatch pending quantity (what's manufactured but not yet sent).
     */
    public function getDispatchPendingQuantityAttribute()
    {
        return max(0, $this->manufactured_quantity - $this->dispatched_quantity);
    }

    /**
     * Get the quantity that is still not manufactured.
     */
    public function getHasManufacturingPendingAttribute()
    {
        return ($this->manufactured_pieces + $this->rejected_pieces) < $this->total_pieces;
    }

    /**
     * Get the pending quantity for this order item.
     *
     * @return int
     */
    public function getPendingQuantityAttribute()
    {
        return max(0, $this->quantity - $this->dispatched_quantity - $this->rejected_quantity);
    }
    
    /**
     * Check if this order item is fully dispatched.
     *
     * @return bool
     */
    public function getIsFullyDispatchedAttribute()
    {
        return $this->dispatched_quantity >= $this->quantity;
    }
    
    /**
     * Check if this order item has pending items to dispatch.
     *
     * @return bool
     */
    public function getHasPendingItemsAttribute()
    {
        return $this->dispatched_quantity < $this->quantity;
    }
    
    /**
     * Get return requests for this order item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_item_id');
    }
}
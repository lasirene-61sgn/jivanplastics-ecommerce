<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_type',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'status',
        'manufacturing_status',
        'manufacturing_team_id',
        'allocated_at',
        'completed_at',
        'dispatched_at',
        'tentative_dispatch_date',
        'payment_method',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip',
        'billing_country',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_zip',
        'shipping_country',
        'original_subtotal',
        'discount_amount',
        'b2b_discount_amount',
        'bank_transfer_discount_amount',
        'other_charges',
        'mfg_edit_request_note',
        'mfg_edit_request_at',
        'mfg_edit_permission_granted',
        'mfg_edit_permission_count',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'allocated_at' => 'datetime',
        'completed_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'tentative_dispatch_date' => 'date',
        'original_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'b2b_discount_amount' => 'decimal:2',
        'bank_transfer_discount_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'mfg_edit_request_at' => 'datetime',
        'mfg_edit_permission_granted' => 'boolean',
        'mfg_edit_permission_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
        
        // Award loyalty points when order is completed
        static::updated(function ($order) {
            // Check if the order status changed to completed
            if ($order->isDirty('status') && $order->status === 'completed') {
                $order->awardLoyaltyPoints();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    public function manufacturingTeam()
    {
        return $this->belongsTo(ManufacturingTeam::class);
    }
    
    /**
     * Get the total pending quantity across all items in this order.
     *
     * @return int
     */
    public function getTotalPendingQuantityAttribute()
    {
        return $this->items->sum('pending_quantity');
    }

    /**
     * Get the total rejected quantity across all items in this order.
     *
     * @return int
     */
    public function getTotalRejectedQuantityAttribute()
    {
        return $this->items->sum('rejected_quantity');
    }
    
    /**
     * Get the total dispatched quantity across all items in this order.
     *
     * @return int
     */
    public function getTotalDispatchedQuantityAttribute()
    {
        return $this->items->sum('dispatched_quantity');
    }
    
    /**
     * Check if this order is fully dispatched.
     *
     * @return bool
     */
    public function getIsFullyDispatchedAttribute()
    {
        return $this->total_pending_quantity <= 0;
    }
    
    /**
     * Check if this order has pending items to dispatch.
     *
     * @return bool
     */
    public function getHasPendingItemsAttribute()
    {
        return $this->total_pending_quantity > 0;
    }
    
    /**
     * Get return requests for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }
    
    /**
     * Get dispatch images for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispatchImages()
    {
        return $this->hasMany(DispatchImage::class);
    }

    /**
     * Get invoices for this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    /**
     * Get the total discount amount for the order.
     *
     * @return float
     */
    public function getOrderDiscountTotalAttribute()
    {
        return (float) ($this->discount_amount ?? 0);
    }
    
    /**
     * Calculate loyalty points for the order.
     *
     * @return int
     */
    public function calculateLoyaltyPoints()
    {
        // Only B2B customers (dealers) earn loyalty points
        if ($this->customer_type !== 'dealer') {
            return 0;
        }
        
        // Only orders above ₹2000 earn points
        if ($this->total <= 2000) {
            return 0;
        }
        
        // 1 point per ₹1000 on the total order value
        return floor($this->total / 1000);
    }
    
    /**
     * Award loyalty points to the customer for this order.
     *
     * @return void
     */
    public function awardLoyaltyPoints()
    {
        // Only B2B customers (dealers) earn loyalty points
        if ($this->customer_type !== 'dealer') {
            return;
        }
        
        $points = $this->calculateLoyaltyPoints();
        
        if ($points > 0 && $this->customer) {
            $this->customer->addLoyaltyPoints($points);
        }
    }
}
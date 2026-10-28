<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id', 'size', 'thickness', 'color', 
        'piece_price', 'total_pieces', 'gst_percentage', 'special_discount', 
        'dealer_special_discounts', 'dealer_discounts', 'total_price'
    ];

    protected $casts = [
        'piece_price' => 'decimal:2',
        'total_pieces' => 'integer',
        'gst_percentage' => 'decimal:2',
        'special_discount' => 'decimal:2',
        'dealer_special_discounts' => 'array',
        'dealer_discounts' => 'array',
        'total_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get base carton price without GST and discounts.
     */
    public function getBasePriceAttribute()
    {
        return (float) ($this->piece_price * $this->total_pieces);
    }

    /**
     * Get the visible Dealer Discount percentage for a specific dealer on this variation.
     * Falls back to product/category dealer discount if not explicitly overridden on this variation.
     *
     * @param  \App\Models\Customer|int|null  $customer
     * @return float
     */
    public function getDealerDiscountForDealer($customer = null)
    {
        if (!$customer) return 0;
        $customerId = is_object($customer) ? $customer->id : (int) $customer;
        $discounts = $this->dealer_discounts ?? [];
        if (isset($discounts[$customerId]) && $discounts[$customerId] !== '' && $discounts[$customerId] !== null && is_numeric($discounts[$customerId])) {
            return (float) $discounts[$customerId];
        }

        return $this->product ? (float) $this->product->getB2BDiscountPercentage($customer) : 0;
    }

    /**
     * Get the special discount percentage assigned to a specific dealer for this variation.
     *
     * @param  \App\Models\Customer|int|null  $customer
     * @return float|null
     */
    public function getSpecialDiscountForDealer($customer = null)
    {
        if (!$customer) return null;
        $customerId = is_object($customer) ? $customer->id : (int) $customer;
        $discounts = $this->dealer_special_discounts ?? [];
        if (isset($discounts[$customerId]) && $discounts[$customerId] !== '' && $discounts[$customerId] !== null && is_numeric($discounts[$customerId])) {
            return (float) $discounts[$customerId];
        }
        return null;
    }

    /**
     * Get effective B2B discount percentage for a dealer on this variation.
     * Combines visible Dealer Discount and any Special Discount override.
     *
     * @param  \App\Models\Customer|null  $customer
     * @return float
     */
    public function getEffectiveB2BDiscountPercentage($customer = null)
    {
        $basePrice = $this->base_price;
        if ($basePrice <= 0) return 0;

        $netPrice = $this->getB2BDiscountedPrice($customer);
        return round((($basePrice - $netPrice) / $basePrice) * 100, 2);
    }

    /**
     * Calculate B2B discounted carton rate for this variation.
     * Applies Dealer Discount first, then any Special Discount.
     *
     * @param  \App\Models\Customer|null  $customer
     * @return float
     */
    public function getB2BDiscountedPrice($customer = null)
    {
        $basePrice = $this->base_price;
        $dealerDiscount = $this->getDealerDiscountForDealer($customer);
        $priceAfterDealer = $basePrice - ($basePrice * $dealerDiscount / 100);

        $specialDiscount = $this->getSpecialDiscountForDealer($customer);
        if ($specialDiscount !== null && $specialDiscount > 0) {
            return $priceAfterDealer - ($priceAfterDealer * $specialDiscount / 100);
        }

        return $priceAfterDealer;
    }

    /**
     * Calculate B2B net piece rate for this variation.
     *
     * @param  \App\Models\Customer|null  $customer
     * @return float
     */
    public function getB2BPiecePrice($customer = null)
    {
        $pieces = max(1, (int) ($this->total_pieces ?? 1));
        return $this->getB2BDiscountedPrice($customer) / $pieces;
    }
}

<?php

namespace App\Models;

use App\Models\DealerDiscount;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'image_path',
        'price',
        'gst_percentage',
        'category_id',
        'subcategory_id',
        'sub_subcategory_id',
        'is_active',
        'min_order_qty',
        'max_order_qty',
        'min_order_qty_b2b',
        'max_order_qty_b2b',
        'min_order_qty_b2c',
        'max_order_qty_b2c',
        'per_quantity_pieces',
        'piece_price',
        'size',
        'thickness',
        'color',
        'thickness_pieces',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'piece_price' => 'decimal:2',
        'per_quantity_pieces' => 'integer',
        'is_active' => 'boolean',
        'thickness_pieces' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the subcategory that owns the product.
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the sub-subcategory that owns the product.
     */
    public function subSubcategory()
    {
        return $this->belongsTo(SubSubcategory::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the dealer discounts for this product.
     */
    public function dealerDiscounts()
    {
        return $this->morphMany(DealerDiscount::class, 'discountable');
    }

    /**
     * Get the B2B discount percentage for a customer.
     *
     * @param  \App\Models\Customer|null  $customer
     * @return float
     */
    public function getB2BDiscountPercentage($customer = null)
    {
        if ($customer && $customer->customer_type === 'dealer') {
            $productDealerDiscount = $this->dealerDiscounts()
                ->where('customer_id', $customer->id)
                ->where('is_active', true)
                ->first();

            if ($productDealerDiscount) {
                return $productDealerDiscount->discount_percentage;
            }

            if ($this->subSubcategory) {
                $subSubcategoryDealerDiscount = $this->subSubcategory->dealerDiscounts()
                    ->where('customer_id', $customer->id)
                    ->where('is_active', true)
                    ->first();

                if ($subSubcategoryDealerDiscount) {
                    return $subSubcategoryDealerDiscount->discount_percentage;
                }
            }

            if ($this->subcategory) {
                $subcategoryDealerDiscount = $this->subcategory->dealerDiscounts()
                    ->where('customer_id', $customer->id)
                    ->where('is_active', true)
                    ->first();

                if ($subcategoryDealerDiscount) {
                    return $subcategoryDealerDiscount->discount_percentage;
                }
            }

            if ($this->category) {
                $categoryDealerDiscount = $this->category->dealerDiscounts()
                    ->where('customer_id', $customer->id)
                    ->where('is_active', true)
                    ->first();

                if ($categoryDealerDiscount) {
                    return $categoryDealerDiscount->discount_percentage;
                }
            }

            return $this->category->b2b_discount ?? 0;
        }

        return 0;
    }

    /**
     * Calculate the B2B discounted price for this product.
     *
     * @param  \App\Models\Customer|null  $customer
     * @return float
     */
    public function getB2BDiscountedPrice($customer = null)
    {
        $percentage = $this->getB2BDiscountPercentage($customer);
        return $this->price - ($this->price * $percentage / 100);
    }

    /**
     * Calculate the price including GST.
     *
     * @return float
     */
    public function getPriceWithGstAttribute()
    {
        return $this->price + ($this->price * $this->gst_percentage / 100);
    }

    /**
     * Get the GST amount for this product.
     *
     * @return float
     */
    public function getGstAmountAttribute()
    {
        return $this->price * $this->gst_percentage / 100;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ProductFactory::new();
    }
}

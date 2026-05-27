<?php

namespace App\Models;

use App\Models\DealerDiscount;
use Database\Factories\SubSubcategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubSubcategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subcategory_id',
        'name',
        'slug',
        'description',
        'is_active',
        'image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subSubcategory) {
            if (empty($subSubcategory->slug)) {
                $subSubcategory->slug = Str::slug($subSubcategory->name);
            }
        });

        static::updating(function ($subSubcategory) {
            if ($subSubcategory->isDirty('name') && empty($subSubcategory->slug)) {
                $subSubcategory->slug = Str::slug($subSubcategory->name);
            }
        });
    }

    /**
     * Get the subcategory that owns the sub-subcategory.
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the products for the sub-subcategory.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the dealer discounts for this sub-subcategory.
     */
    public function dealerDiscounts()
    {
        return $this->morphMany(DealerDiscount::class, 'discountable');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return SubSubcategoryFactory::new();
    }
}
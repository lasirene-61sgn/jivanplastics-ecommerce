<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company_name',
        'gst_number',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'customer_type',
        'is_active',
        'loyalty_points',
        'is_cod_allowed',
        'bank_transfer_discount',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'loyalty_points' => 'integer',
        'is_cod_allowed' => 'boolean',
        'bank_transfer_discount' => 'decimal:2',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the customer's full address.
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $addressParts = [
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country
        ];

        return implode(', ', array_filter($addressParts));
    }

    /**
     * Scope a query to only include active customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include dealers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDealers($query)
    {
        return $query->where('customer_type', 'dealer');
    }

    /**
     * Scope a query to only include individual customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIndividuals($query)
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Get the orders for the customer.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the dealer discounts for this customer.
     */
    public function dealerDiscounts()
    {
        return $this->hasMany(DealerDiscount::class);
    }
    
    /**
     * Get the reward claims for this customer.
     */
    public function rewardClaims()
    {
        return $this->hasMany(RewardClaim::class);
    }

    /**
     * Add loyalty points to the customer.
     *
     * @param int $points
     * @return void
     */
    public function addLoyaltyPoints($points)
    {
        $this->loyalty_points += $points;
        $this->save();
    }

    /**
     * Deduct loyalty points from the customer.
     *
     * @param int $points
     * @return bool
     */
    public function deductLoyaltyPoints($points)
    {
        if ($this->loyalty_points >= $points) {
            $this->loyalty_points -= $points;
            $this->save();
            return true;
        }
        return false;
    }
}
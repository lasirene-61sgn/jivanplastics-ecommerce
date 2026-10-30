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
        'online_discount',
        'draft_discount',
        'last_login_at',
        'current_login_ip',
        'last_login_ip',
        'login_count',
        'last_device',
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
        'online_discount' => 'decimal:2',
        'draft_discount' => 'decimal:2',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'login_count' => 'integer',
    ];

    /**
     * Get the effective online discount percentage.
     */
    public function getEffectiveOnlineDiscountAttribute(): float
    {
        return (float) ($this->online_discount > 0 ? $this->online_discount : ($this->bank_transfer_discount ?? 0));
    }

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
     * Get the activity logs for this customer.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Record login for this customer and create an activity log.
     */
    public function recordLogin($channel = 'Web', $request = null)
    {
        $request = $request ?? request();
        $userAgent = $request ? $request->userAgent() : '';
        $deviceType = 'PC';
        $browser = 'Chrome';

        if ($userAgent) {
            if (preg_match('/mobile/i', $userAgent)) {
                $deviceType = 'Mobile';
            } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
                $deviceType = 'Tablet';
            }

            if (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
                $browser = 'Internet Explorer';
            } elseif (preg_match('/Edge/i', $userAgent)) {
                $browser = 'Edge';
            } elseif (preg_match('/Chrome/i', $userAgent)) {
                $browser = 'Chrome';
            } elseif (preg_match('/Safari/i', $userAgent)) {
                $browser = 'Safari';
            } elseif (preg_match('/Firefox/i', $userAgent)) {
                $browser = 'Firefox';
            } elseif (preg_match('/Opera/i', $userAgent)) {
                $browser = 'Opera';
            }
        }

        $ip = $request ? ($request->header('CF-Connecting-IP') 
            ?? $request->header('X-Forwarded-For') 
            ?? $request->header('X-Real-IP') 
            ?? $request->ip()) : '127.0.0.1';

        if ($ip && strpos($ip, ',') !== false) {
            $ip = explode(',', $ip)[0];
        }
        $ip = trim($ip ?? '127.0.0.1');

        $deviceSummary = $deviceType . ' (' . $browser . ')';

        $this->last_login_ip = $this->current_login_ip ?? $ip;
        $this->current_login_ip = $ip;
        $this->last_login_at = now();
        $this->login_count = ($this->login_count ?? 0) + 1;
        $this->last_device = $deviceSummary;
        $this->save();

        return ActivityLog::create([
            'customer_id' => $this->id,
            'user_type' => $this->customer_type === 'dealer' ? 'b2b' : 'b2c',
            'action' => 'login',
            'model_type' => 'Customer',
            'model_id' => $this->id,
            'ip_address' => $ip,
            'details' => [
                'name' => $this->name,
                'email' => $this->email,
                'company_name' => $this->company_name,
                'channel' => $channel,
                'device' => $deviceType,
                'browser' => $browser,
            ]
        ]);
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

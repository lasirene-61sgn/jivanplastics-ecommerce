<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardClaim extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id',
        'reward_id',
        'invoice_id',
        'status',
        'admin_notes',
        'claimed_at',
        'processed_at',
        'dispatch_proof_image',
        'invoice_proof_image',
    ];
    
    protected $casts = [
        'claimed_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get the system-generated invoice for this reward claim.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
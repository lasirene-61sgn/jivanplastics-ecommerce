<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'product_id',
        'required_points',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'required_points' => 'integer',
        'is_active' => 'boolean',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function claims()
    {
        return $this->hasMany(RewardClaim::class);
    }
}
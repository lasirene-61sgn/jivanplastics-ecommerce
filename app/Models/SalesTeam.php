<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SalesTeam extends Authenticatable
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
        'department',
        'assigned_dealers',
        'is_active',
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
        'assigned_dealers' => 'array',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get the dealers assigned to this sales team member.
     */
    public function getAssignedDealersList()
    {
        $dealers = $this->assigned_dealers ?? [];
        return is_array($dealers) ? $dealers : [];
    }
    
    /**
     * Assign dealers to this sales team member.
     */
    public function assignDealers(array $dealerIds)
    {
        $this->assigned_dealers = $dealerIds;
        $this->save();
    }
}
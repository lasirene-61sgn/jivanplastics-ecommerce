<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'manufacturing_team_id',
        'pieces_completed',
        'pieces_rejected',
        'quantity_completed',
        'quantity_rejected',
        'rejection_reason',
    ];

    protected $casts = [
        'pieces_completed' => 'integer',
        'pieces_rejected' => 'integer',
        'quantity_completed' => 'decimal:4',
        'quantity_rejected' => 'decimal:4',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function manufacturingTeam()
    {
        return $this->belongsTo(ManufacturingTeam::class);
    }
}

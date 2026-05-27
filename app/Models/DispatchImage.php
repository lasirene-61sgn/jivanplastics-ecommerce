<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DispatchImage extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'image_path',
        'description',
        'uploaded_by',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
    
    /**
     * Get the full URL for the image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }
}

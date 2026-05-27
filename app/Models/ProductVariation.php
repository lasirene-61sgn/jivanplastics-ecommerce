<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id', 'size', 'thickness', 'color', 
        'piece_price', 'total_pieces', 'gst_percentage', 'total_price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'order_id', 'status', 'total_price',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Define the many-to-many relationship with Product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_product')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }
}
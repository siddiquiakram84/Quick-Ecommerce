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
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     // Event listener to update total_price before saving
    //     static::saving(function ($cart) {
    //         $cart->total_price = $cart->calculateTotalPrice();
    //     });
    // }

    // public function calculateTotalPrice()
    // {
    //     return $this->products->sum(function ($product) {
    //         return $product->pivot->quantity * $product->pivot->unit_price;
    //     });
    // }
}
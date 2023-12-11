<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'quantity_in_stock', 'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define the many-to-many relationship with Cart
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_product')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity');
    }

}

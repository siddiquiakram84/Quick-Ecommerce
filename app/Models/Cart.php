<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'cartitem',
        'status',
        'total_price',
    ];

    protected $casts = [
        'status' => 'integer', 'cartitem' => 'json',
    ];

    // Define the many-to-many relationship with Product
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'unit_price');
    }


        // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

}
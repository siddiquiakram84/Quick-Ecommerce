<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_price', 'status', 'payment_status', 'delivery_address', 'delivery_method',
        // Add other attributes here if needed
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_amount', 'status', 'address'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Add inside any model class
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

// app/Models/OrderItem.php
class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Add inside any model class
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'product_id', 'price','weight'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productInstances()
    {
        return $this->hasMany(OrderProductInstance::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

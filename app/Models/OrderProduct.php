<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'product_instance_id', 'price'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function productInstance()
    {
        return $this->belongsTo(ProductInstance::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductInstance extends Model
{
    use HasFactory;


    protected $fillable = ['product_id',
     'order_id', 
     'unique_identifier',
     'product_instance_id',
     'order_product_id'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

}

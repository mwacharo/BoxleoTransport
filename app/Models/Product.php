<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'sku','warehouse_id','branch_id', 'name', 'description', 'price', 'quantity','vendor_id','active','virtual','user_id','low_stockvalue'
    ];

    public function productInstances()
    {
        return $this->hasMany(ProductInstance::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function returnProducts()
    {
        return $this->belongsToMany(ReturnProduct::class, 'return_products')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public static $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255',
        // 'vendor_id' => 'required|string|max:255',


    ];
}

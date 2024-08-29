<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        // 'expiry_date','batch_number','height','width','length','weight','sku','warehouse_id','branch_id', 'name', 'description', 'price', 'quantity','vendor_id','active','virtual','user_id','low_stockvalue'


        'sku',
        'warehouse_id',
        'branch_id',
        'name',
        'description',
        'price',
        'quantity',
        'vendor_id',
        'active',
        'virtual',
        'user_id',
        'low_stockvalue',
        'weight',
        'length',
        'width',
        'height',
        'batch_number',
        'expiry_date',
        'opening_quantity',
        'return_quantity',
        'quantity_in_hand',
        'quantity_in',
        'quantity_out',
        'pilferages_destroyed',
        'reorder_point',
        'unit_buying_price',
        'unit_price',
        'quantity_issued',
        'quanity_delivered',
        'inventory_value',
        'sales_value',
        'quantity_remaining',
        'status',
        'physical_count',
        'variance',


        
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
        // 'sku' => 'required|string|max:255',
        // 'vendor_id' => 'required|string|max:255',


    ];




      // Reserve stock
      public function reserveStock($quantity)
      {
          if ($this->quantity_remaining >= $quantity) {
              $this->quantity_reserved += $quantity;
              $this->quantity_remaining -= $quantity;
              $this->save();
              return true;
          }
          return false;
      }
  
      // Release reserved stock (e.g., when an order is canceled)
      public function releaseStock($quantity)
      {
          $this->quantity_reserved -= $quantity;
          $this->quantity_remaining += $quantity;
          $this->save();
      }
  
      // Deduct reserved stock when an order is confirmed (picked/packed)
    public function confirmStock($quantity)
    {
        $this->quantity_inhand -= $quantity;
        $this->quantity_reserved -= $quantity;
        $this->save();
    }
}

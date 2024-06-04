<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'barcode', 'batch_number', 'expiry_date', 'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // public function activities()
    // {
    //     return $this->hasMany(ProductActivity::class);
    // }
}

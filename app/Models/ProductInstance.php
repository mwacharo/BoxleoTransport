<?php

namespace App\Models;

// use Google\Service\ChromeUXReport\Bin;
use App\Models\Bin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'barcode', 'batch_number', 'expiry_date','bin_location', 'status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function orderProductInstances()
    {
        return $this->hasMany(OrderProductInstance::class);
    }


    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
// country 
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

//warehouse 
public function warehouse(){
    return $this->belongsTo(Warehouse::class);
}

// bin 
public function bin(){
    return $this->belongsTo(Bin::class);
}


    // public function activities()
    // {
    //     return $this->hasMany(ProductActivity::class);
    // }
}

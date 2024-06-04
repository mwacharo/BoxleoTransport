<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'return_id', 'product_id', 'quantity'
    ];

    public function return()
    {
        return $this->belongsTo(ReturnOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductInstance::class);
    }
    
    
}

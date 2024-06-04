<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'return_date', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function returnProducts()
    {
        return $this->hasMany(ReturnProduct::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_no',
        'pod_path',
    ];

    // public function order()
    // {
    //     return $this->belongsTo(Order::class);
    // }
}

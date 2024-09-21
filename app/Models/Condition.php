<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable= [
        'region',
        'route',
        'rate_3t',
        'rate_5t',
        'rate_7t',
        'rate_10t',
        'service_id ','condition_amount','condition_percentage','vendor_id','branch_id'

    ];
}



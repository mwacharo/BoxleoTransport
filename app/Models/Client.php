<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        // 'user_id',
        'branch_id',
        'name',
        'email',
        'phone',
        'alt_phone',
        'address',
        'latitude',
        'longitude',
        'gender',
        'city',
        // 'group_id',
        'payment_type',
        'vendor_id',
    ];

    protected $dates = ['deleted_at'];


    public static $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'phone' => 'required|string|max:255',

    ];

}

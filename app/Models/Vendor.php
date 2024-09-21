<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [

        'name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'email',
        'email_verified_at',
        'password',
        'active',
        'sheet_update',
        'send_sms',
        'telegram_notifications',
        'shopify_email',
        'order_no_start',
        'order_no_end',
        'autogenerate',
        'portal_active',
        'terms',
        'company_id',
        'branch_id',
        'order_prefix',
        'remember_token',
        'created_at',
        'updated_at',
        'date',
        'last_identifier'


    ];

    // protected $dates = [

    // ];


    public static $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'phone' => 'required|string|max:255',
        'last_identifier' => 'string|max:255',

    ];

    public function sheet(){
        return $this->hasMany(Sheet::class);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function service()
    {
        return $this->hasMany(Service::class);
    }

}

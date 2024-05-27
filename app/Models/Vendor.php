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
        'ou_id',
        'order_prefix',
        'remember_token',
        'created_at',
        'updated_at',
        'date'
    
    
    ];

    // protected $dates = [
      
    // ];


    public static $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'phone' => 'required|string|max:255',

    ];

    public function sheet(){
        return $this->hasMany(Sheet::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rider extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'ou_id',
        'email_verified_at',
        'password ',
        'remember_token'
    ];



    public static $rules = [
        'name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'email' => 'required|string|max:255',
        'phone' => 'required|string|max:255',

    ];
    protected $dates = ['deleted_at'];
}

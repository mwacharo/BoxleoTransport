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
        'branch_id',
        'email_verified_at',
        'password ',
        'remember_token',
        'geofence_id',
    ];



    public static $rules = [
    

    ];
    protected $dates = ['deleted_at'];
}

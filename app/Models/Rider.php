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
        'latitude',
        'longitude',
        'status',
        'branch_id',
        'email_verified_at',
        'password ',
        'remember_token',
        'geofence_id',
        'status',
        'clearance_status',
        'comment'
    ];



    public static $rules = [


    ];
    protected $dates = ['deleted_at'];



    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function geofence(){
        return $this->belongsTo(Geofence::class);

    }
}

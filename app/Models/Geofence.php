<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geofence extends Model
{
    use HasFactory;


    protected $fillable = ['path','name'];

    protected $casts = [
        'path' => 'array',
    ];



        public static $rules = [
    

        ];
}

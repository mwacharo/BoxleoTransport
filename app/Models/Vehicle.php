<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'license_plate',
        'capacity',
        'status',
        'current_location',
        'address',
        'driver_id',
        'depot_id',
    ];

    protected $casts = [
        'current_location' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }


        public static $rules = [


        ];
}

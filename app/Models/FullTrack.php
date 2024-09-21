<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullTrack extends Model
{
    use HasFactory;


        // Define the table name if it does not follow Laravel's convention
        protected $table = 'full_truck_rates';

        // The attributes that are mass assignable
        protected $fillable = [
            'region',
            'route',
            'rate_3t',
            'rate_5t',
            'rate_7t',
            'rate_10t',
            'vendor_id',
            // 'service_id'
        ];
    
        // Disable timestamps if not used
        public $timestamps = true;
}

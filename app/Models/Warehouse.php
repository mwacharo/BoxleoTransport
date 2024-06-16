<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id', 'ou_id', 'name', 'phone', 'location', 
        'length', 'width', 'height', 'non_storage', 'capacity', 'code'
    ];

    public function bins()
    {
        return $this->hasMany(Bin::class);
    }
}

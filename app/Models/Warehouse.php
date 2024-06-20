<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;



    protected $fillable = [
        'user_id', 'branch_id', 'name', 'phone', 'address',
        'length', 'width', 'height', 'non_storage', 'capacity', 'code','email'
    ];

    public function bins()
    {
        return $this->hasMany(Bin::class);
    }

    public static $rules = [
        'name' => 'required|string|max:255',


    ];
}

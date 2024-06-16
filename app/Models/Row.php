<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bays()
    {
        return $this->hasMany(Bay::class);
    }

    public static $rules = [
        'name' => 'required|string|max:255',


    ];
}

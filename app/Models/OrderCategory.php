re<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCategory extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];
    protected $dates = ['deleted_at'];


    public static $rules = [
        // 'name' => 'required|string|max:255',
        // 'description' => 'required|string|max:255',
    ];
}

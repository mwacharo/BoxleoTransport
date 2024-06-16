<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bay extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'row_id'];

    public function row()
    {
        return $this->belongsTo(Row::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }

    public static $rules = [
        'name' => 'required|string|max:255',

    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;


    use HasFactory;

    protected $fillable = ['code', 'name', 'bay_id'];

    public function bay()
    {
        return $this->belongsTo(Bay::class);
    }

    public static $rules = [
        'name' => 'required|string|max:255',
      

    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'warehouse_id', 'row_id', 'bay_id',
        'level_id', 'area_id', 'quantity'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}

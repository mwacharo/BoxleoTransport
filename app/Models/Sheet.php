<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class sheet extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'sheet_name',
        'post_spreadsheet_id',
        'active',
        'auto_sync',
        'sync_all',
        'sync_interval',
        'last_order_synced',
        'last_order_upload',
        'last_product_synced',
        'is_current',
        'order_prefix',
        'vendor_id',
        'lastUpdatedOrderNumber',
        'branch_id',
        'created_at',
        'updated_at',
    ];

    public static $rules = [
        'sheet_name' => 'required|string|max:255',
        'post_spreadsheet_id' => 'required|string|max:255',
        'vendor_id' => 'required|max:255',
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

}

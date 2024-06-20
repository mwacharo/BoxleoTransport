<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Order extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'order_no',
        'order_type',
        'client_name',
        'address',
        'country',
        'phone',
        'alt_phone',
        'city',
        'sku_no',
        'product_name',
        'quantity',
        'boxes',
        'weight',
        'status',
        'delivery_date',
        'pod',
        'special_instruction',
        'distance',
        'pod_returned',
        'client_id',
        'agent_id',
        'rider_id',
        'driver_id',
        'total_price',
        'invoice_value',
        'amount_paid',
        'sub_total',
        'tracking_no',
        'waybill_no',
        'customer_notes',
        'discount',
        'shipping_charges',
        'charges',
        'delivery_date',
        'delivery_status',
        'warehouse_id',
        'vendor_id',
        'payment_method',
        'payment_id',
        'mpesa_code',
        'platform',
        'cancel_notes',
        'is_return_waiting_for_approval',
        'is_salesreturn_allowed',
        'is_emailed',
        'is_dropshipped',
        'is_cancel_item_waiting_for_approval',
        'track_inventory',
        'confirmed',
        'delivered',
        'returned',
        'cancelled',
        'invoiced',
        'packed',
        'printed',
        'print_count',
        'sticker_printed',
        'prepaid',
        'paid',
        'weight',
        'return_count',
        'dispatched_on',
        'return_date',
        'delivered_on',
        'returned_on',
        'cancelled_on',
        'printed_at',
        'print_no',
        'sticker_at',
        'recall_date',
        'history_comment',
        'return_notes',
        'ou_id',
        'receiver_id',
        'receiver_name',
        'receiver_phone',
        'receiver_email',
        'receiver_address',
        'pickup_address',
        'pickup_phone',
        'pickup_shop',
        'upsell',
        'pickup_city',
        'user_id',
        'schedule_date',
        'longitude',
        'latitude',
        'geocoded',
        'loading_no',
        'branch_id'

    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class);

    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnProduct::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public static $rules = [
        // 'name' => 'required|string|max:255',
        // 'address' => 'required|string|max:255',
        // 'email' => 'required|string|max:255',
        // 'phone' => 'required|string|max:255',

    ];
}

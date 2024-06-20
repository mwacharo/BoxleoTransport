<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
  public function run(): void

  {

      $orderData = [

          'order_no' => '123456',

          'order_type' => 'sales',

          'client_name' => 'John Doe',

          'address' => '123 Main St',

          'country' => 'USA',

          'phone' => '555-555-1212',

          'alt_phone' => '555-555-1313',

          'city' => 'New York',

          'sku_no' => 'ABC123',

          'product_name' => 'Test Product',

          'quantity' => 10,

          'boxes' => 2,

          'weight' => 50,

          'status' => 'pending',

          'delivery_date' => '2023-01-01',

          'pod' => '1234567890',

          'special_instruction' => 'Deliver to front door',

          'distance' => 10,

          'pod_returned' => false,

          'client_id' => 1,

          'agent_id' => 1,

          'rider_id' => 1,

          'driver_id' => 1,

          'total_price' => 100,

          'invoice_value' => 100,

          'amount_paid' => 100,

          'sub_total' => 90,

          'discount' => 10,

          'shipping_charges' => 10,

          'charges' => 10,

          'warehouse_id' => 1,

          'vendor_id' => 1,

          'payment_method' => 'credit_card',

          'payment_id' => 1,

          'mpesa_code' => '123456',

          'platform' => 'web',

          'cancel_notes' => 'Customer cancelled order',

          'is_return_waiting_for_approval' => false,

          'is_salesreturn_allowed' => false,

          'is_emailed' => false,

          'is_dropshipped' => false,

          'is_cancel_item_waiting_for_approval' => false,

          'track_inventory' => false,

          'confirmed' => false,

          'delivered' => false,

          'returned' => false,

          'cancelled' => false,

          'invoiced' => false,

          'packed' => false,

          'printed' => false,

          'print_count' => 1,

          'sticker_printed' => false,

          'prepaid' => false,

          'paid' => false,

          'return_count' => 0,

          'dispatched_on' => '2023-01-03',

          'return_date' => '2023-01-04',

          'delivered_on' => '2023-01-05',

          'returned_on' => '2023-01-06',

          'cancelled_on' => '2023-01-07',

          'printed_at' => '2023-01-08',

          'print_no' => '123456',

          'sticker_at' => '2023-01-09',

          'recall_date' => '2023-01-10',

          'history_comment' => 'Order created',

          'return_notes' => 'Customer returned order',

          'branch_id' => 1,

          'receiver_id' => 1,

          'receiver_name' => 'John Doe',

          'receiver_phone' => '555-555-1212',

          'receiver_email' => 'john@example.com',

          'receiver_address' => '123 Main St',

          'pickup_address' => '123 Main St',

          'pickup_phone' => '555-555-1212',

          'pickup_shop' => 'Test Shop',

          'upsell' => false,

          'pickup_city' => 'New York',

          'user_id' => 1,

          'schedule_date' => '2023-01-11',

          'longitude' => 40.7128,

          'latitude' => 74.0060,

          'geocoded' => true,

          'loading_no' => 'ABC123',

      ];


      Order::create($orderData);

  }
}

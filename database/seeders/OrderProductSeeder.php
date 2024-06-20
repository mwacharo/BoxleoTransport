<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderProduct ;

class OrderProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void

      {

          $orderProducts = [

              [

                  'order_id' => 1,

                  'product_instance_id' => 1,

                  'price' => 10.99,

              ],

              [

                  'order_id' => 1,

                  'product_instance_id' => 2,

                  'price' => 5.99,

              ],

              [

                  'order_id' => 2,

                  'product_instance_id' => 3,

                  'price' => 7.99,

              ],

              [

                  'order_id' => 3,

                  'product_instance_id' => 1,

                  'price' => 10.99,

              ],

              // Add more order products as needed

          ];


          foreach ($orderProducts as $orderProduct) {

              OrderProduct::create($orderProduct);

          }

      }
}

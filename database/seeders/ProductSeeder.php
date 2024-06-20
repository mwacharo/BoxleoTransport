<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
class ProductSeeder extends Seeder
{
  public function run(): void

 {

     $products = [

         [

             'sku' => 'PROD-001',

             'warehouse_id' => 1,

             'ou_id' => 1,

             'name' => 'Product 1',

             'description' => 'This is product 1',

             'price' => 19.99,

             'quantity' => 100,

             'vendor_id' => 1,

             'active' => true,

             'virtual' => false,

             'user_id' => 1,

             'low_stock_value' => 20,

         ],

         [

             'sku' => 'PROD-002',

             'warehouse_id' => 1,

             'ou_id' => 1,

             'name' => 'Product 2',

             'description' => 'This is product 2',

             'price' => 9.99,

             'quantity' => 50,

             'vendor_id' => 1,

             'active' => true,

             'virtual' => false,

             'user_id' => 1,

             'low_stock_value' => 10,

         ],

         [

             'sku' => 'PROD-003',

             'warehouse_id' => 2,

             'ou_id' => 2,

             'name' => 'Product 3',

             'description' => 'This is product 3',

             'price' => 29.99,

             'quantity' => 200,

             'vendor_id' => 2,

             'active' => true,

             'virtual' => false,

             'user_id' => 2,

             'low_stock_value' => 30,

         ],

         // Add more products as needed

     ];


     foreach ($products as $product) {

         Product::create($product);

     }

 }
}

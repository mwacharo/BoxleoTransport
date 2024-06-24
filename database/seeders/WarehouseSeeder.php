<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;


class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void

     {

         $warehouses = [

             [

                 'user_id' => 1,

                 'branch_id' => 1,

                 'name' => 'Warehouse 1',

                 'phone' => '555-555-1212',

                 'address' => '123 Main St',

                 'length' => 100,

                 'width' => 50,

                 'height' => 20,

                 'non_storage' => 10,

                 'capacity' => 10000,

                 'code' => 'W1',

                 'email' => 'warehouse1@example.com'

             ],

             [

                 'user_id' => 1,

                 'branch_id' => 2,

                 'name' => 'Warehouse 2',

                 'phone' => '555-555-1213',

                 'address' => '456 Elm St',

                 'length' => 150,

                 'width' => 75,

                 'height' => 25,

                 'non_storage' => 15,

                 'capacity' => 15000,

                 'code' => 'W2',

                 'email' => 'warehouse2@example.com'

             ],

             // Add more warehouses as needed

         ];


         foreach ($warehouses as $warehouse) {

             Warehouse::create($warehouse);

         }

     }
}

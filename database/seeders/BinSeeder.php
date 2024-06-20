<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bin;

class BinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


     public function run(): void

   {

       $bins = [

           [

               'code' => 'BIN-001',

               'name' => 'Bin 1',

               'warehouse_id' => 1,

               'row_id' => 1,

               'bay_id' => 1,

               'level_id' => 1,

               'area_id' => 1,

               'quantity' => 0

           ],

           // [
           //
           //     'code' => 'BIN-002',
           //
           //     'name' => 'Bin 2',
           //
           //     'warehouse_id' => 1,
           //
           //     'row_id' => 1,
           //
           //     'bay_id' => 1,
           //
           //     'level_id' => 1,
           //
           //     'area_id' => 1,
           //
           //     'quantity' => 0
           //
           // ],
           //
           // [
           //
           //     'code' => 'BIN-003',
           //
           //     'name' => 'Bin 3',
           //
           //     'warehouse_id' => 1,
           //
           //     'row_id' => 2,
           //
           //     'bay_id' => 2,
           //
           //     'level_id' => 2,
           //
           //     'area_id' => 2,
           //
           //     'quantity' => 0
           //
           // ],
           //
           // [
           //
           //     'code' => 'BIN-004',
           //
           //     'name' => 'Bin 4',
           //
           //     'warehouse_id' => 2,
           //
           //     'row_id' => 3,
           //
           //     'bay_id' => 3,
           //
           //     'level_id' => 3,
           //
           //     'area_id' => 3,
           //
           //     'quantity' => 0
           //
           // ],

           // Add more bins as needed

       ];


       foreach ($bins as $bin) {

           Bin::create($bin);

       }

   }



}

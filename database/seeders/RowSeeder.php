<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Row; 

class RowSeeder extends Seeder
{
  public function run(): void

 {

     $rows = [

         [

             'code' => 'ROW-001',

             'name' => 'Row 1',

             'warehouse_id' => 1,

         ],

         [

             'code' => 'ROW-002',

             'name' => 'Row 2',

             'warehouse_id' => 1,

         ],

         [

             'code' => 'ROW-003',

             'name' => 'Row 3',

             'warehouse_id' => 2,

         ],

         // Add more rows as needed

     ];


     foreach ($rows as $row) {

         Row::create($row);

     }

 }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area; 


class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void

     {

         $areas = [
             [

                 'code' => 'A1',

                 'name' => 'Area 1',

                 'warehouse_id' => 1

             ],

             [

                 'code' => 'A2',

                 'name' => 'Area 2',

                 'warehouse_id' => 1

             ],

             [

                 'code' => 'B1',

                 'name' => 'Area 3',

                 'warehouse_id' => 2

             ],

             [

                 'code' => 'B2',

                 'name' => 'Area 4',

                 'warehouse_id' => 2

             ],

             // Add more areas as needed

         ];


         foreach ($areas as $area) {

             Area::create($area);

         }

     }
}

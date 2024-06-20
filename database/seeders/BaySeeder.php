<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bay; 

class BaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void

      {

          $bays = [

              [

                  'code' => 'B1',

                  'name' => 'Bay 1',

                  'row_id' => 1

              ],

              [

                  'code' => 'B2',

                  'name' => 'Bay 2',

                  'row_id' => 1

              ],

              [

                  'code' => 'B3',

                  'name' => 'Bay 3',

                  'row_id' => 2

              ],

              [

                  'code' => 'B4',

                  'name' => 'Bay 4',

                  'row_id' => 2

              ],

              // Add more bays as needed

          ];


          foreach ($bays as $bay) {

              Bay::create($bay);

          }

      }
}

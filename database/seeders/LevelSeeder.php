<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
  public function run(): void

{

    $levels = [

        [

            'code' => 'L1',

            'name' => 'Level 1',

            'bay_id' => 1,

        ],

        [

            'code' => 'L2',

            'name' => 'Level 2',

            'bay_id' => 1,

        ],

        [

            'code' => 'L3',

            'name' => 'Level 3',

            'bay_id' => 2,

        ],

        // Add more levels as needed

    ];


    foreach ($levels as $level) {

        Level::create($level);

    }

}
}

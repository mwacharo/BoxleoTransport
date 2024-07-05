<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rider;

class RiderSeeder extends Seeder
{
  public function run(): void

{

    $riders = [

        [

            'name' => 'John Doe',

            'email' => 'johndoe@example.com',

            'phone' => '1234567890',

            'address' => '123 Main St',

            'branch_id' => 1,
            'geofence_id' => 1,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),


            // 'password' => Hash::make('password'),

            // 'remember_token' => str_random(10),

        ],

        [

            'name' => 'Jane Doe',

            'email' => 'janedoe@example.com',

            'phone' => '0987654321',

            'address' => '456 Elm St',

            'branch_id' => 2,

            'email_verified_at' => now(),
            'password' => bcrypt('password'),


            // 'password' => Hash::make('password'),

            // 'remember_token' => str_random(10),

        ],

        // Add more riders as needed

    ];


    foreach ($riders as $rider) {

        Rider::create($rider);

    }

}
}

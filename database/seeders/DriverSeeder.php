<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Driver;


class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void

      {

          $drivers = [

              [

                  'name' => 'John Doe',

                  'email' => 'johndoe@example.com',

                  'phone' => '123-456-7890',

                  'address' => '123 Main St',

                  'ou_id' => 1,

                  'email_verified_at' => now(),

                  // 'password' => Hash::make('password'),

                  'password' => bcrypt('password'),

                  // 'remember_token' => Str::random(10),

              ],

              [

                  'name' => 'Jane Doe',

                  'email' => 'janedoe@example.com',

                  'phone' => '555-123-4567',

                  'address' => '456 Elm St',

                  'ou_id' => 1,

                  'email_verified_at' => now(),


                   'password' => bcrypt('password'),
                  // 'remember_token' => Str::random(10),

              ],

              [

                  'name' => 'Bob Smith',

                  'email' => 'bobsmith@example.com',

                  'phone' => '555-789-0123',

                  'address' => '789 Oak St',

                  'ou_id' => 2,

                  'email_verified_at' => now(),

                  // 'password' => Hash::make('password'),
                  'password' => bcrypt('password'),


                  // 'remember_token' => Str::random(10),

              ],

              // Add more drivers as needed

          ];


          foreach ($drivers as $driver) {

              Driver::create($driver);

          }

      }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
  public function run(): void

   {

       $clients = [

           [

               'branch_id' => 1,

               'name' => 'John Doe',

               'email' => 'johndoe@example.com',

               'phone' => '123-456-7890',

               'alt_phone' => '098-765-4321',

               'address' => '123 Main St',

               'gender' => 'Male',

               'city' => 'New York',

               'payment_type' => 'Cash',

               'vendor_id' => 1,

           ],

           [

               'branch_id' => 1,

               'name' => 'Jane Doe',

               'email' => 'janedoe@example.com',

               'phone' => '555-123-4567',

               'alt_phone' => '555-901-2345',

               'address' => '456 Elm St',

               'gender' => 'Female',

               'city' => 'Los Angeles',

               'payment_type' => 'Credit',

               'vendor_id' => 2,

           ],

           [

               'branch_id' => 2,

               'name' => 'Bob Smith',

               'email' => 'bobsmith@example.com',

               'phone' => '555-789-0123',

               'alt_phone' => '555-456-7890',

               'address' => '789 Oak St',

               'gender' => 'Male',

               'city' => 'Chicago',

               'payment_type' => 'Check',

               'vendor_id' => 3,

           ],

           // Add more clients as needed

       ];


       foreach ($clients as $client) {

           Client::create($client);

       }

   }
}

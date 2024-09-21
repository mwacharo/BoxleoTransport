<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     //
    // }


    public function run(): void
    {
        DB::table('services')->insert([
            ['service_name' => 'inbound'],
            ['service_name' => 'outbound'],
            ['service_name' => 'pickup fees'],
            ['service_name' => 'storage'],
            ['service_name' => 'returns'],
            ['service_name' => 'full truck'],
            ['service_name' => 'weight'],
            ['service_name' => 'distance'],
        ]);
    }
}

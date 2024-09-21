<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FullTrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('full_tracks')->insert([
            [
                'region' => 'Eastern Region',
                'route' => 'Tala, Machakos, Kitui, Makueni, Emali',
                'rate_3t' => 22998,
                'rate_5t' => 26109,
                'rate_7t' => 28404,
                'rate_10t' => 34200,
            ],
            [
                'region' => 'Mountain Region',
                'route' => 'Thika, Kabati, Muranga, Kagio, Kirinyaga, Nyeri, Embu, Meru',
                'rate_3t' => 27675,
                'rate_5t' => 31509,
                'rate_7t' => 34884,
                'rate_10t' => 40500,
            ],
            [
                'region' => 'South Rift',
                'route' => 'Nakuru, Kericho, Nyandarua, Samburu',
                'rate_3t' => 24165,
                'rate_5t' => 27459,
                'rate_7t' => 30294,
                'rate_10t' => 33300,
            ],
        ]);
    }
}

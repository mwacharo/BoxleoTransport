<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve the service IDs by name to attach conditions
        $services = Service::whereIn('service_name', [
            'inbound',
            'outbound',
            'pickup fees',
            'storage',
            'returns',
            'full truck',
            'weight',
            'distance',
        ])->get()->keyBy('service_name');

        // Insert conditions for each service
        DB::table('conditions')->insert([
            // Example conditions for INBOUND service
            [
                'service_id' => $services['inbound']->id,
                'condition_amount' => 250.00,  // First 5kg condition
                'condition_percentage' => null,  // No percentage for this condition
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],
            [
                'service_id' => $services['inbound']->id,
                'condition_amount' => null,
                'condition_percentage' => 6.00,  // 6% of invoice value condition
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],

            // Example conditions for OUTBOUND service
            [
                'service_id' => $services['outbound']->id,
                'condition_amount' => 280.00,  // Fixed amount per box
                'condition_percentage' => null,
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],
            [
                'service_id' => $services['outbound']->id,
                'condition_amount' => null,
                'condition_percentage' => 5.00,  // 5% of invoice value condition
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],

            // Example conditions for FULL TRUCK service (can be expanded as needed)
            [
                'service_id' => $services['full truck']->id,
                'condition_amount' => null,
                'condition_percentage' => null,
                'region' => 'Eastern Region',
                'route' => 'Tala, Machakos, Kitui, Makueni, Emali',
                'rate_3t' => 22998,
                'rate_5t' => 26109,
                'rate_7t' => 28404,
                'rate_10t' => 34200,
            ],

            // [
            //     'service_id' => $services['full truck']->id,
            //     'region' => 'South Rift',
            //     'route' => 'Nakuru, Kericho, Nyandarua, Samburu',
            //     'rate_3t' => 24165,
            //     'rate_5t' => 27459,
            //     'rate_7t' => 30294,
            //     'rate_10t' => 33300,
            // ],


            // Example conditions for WEIGHT service
            [
                'service_id' => $services['weight']->id,
                'condition_amount' => 250.00,  // First 5kg
                'condition_percentage' => 8.00,  // 8% of invoice value for extra weight
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],

            // Example conditions for DISTANCE service
            [
                'service_id' => $services['distance']->id,
                'condition_amount' => 150.00,  // For extra km beyond the first 10km
                'condition_percentage' => null,
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],
            [
                'service_id' => $services['distance']->id,
                'condition_amount' => null,
                'condition_percentage' => 5.50,  // 5.5% of invoice value
                'region' => null,
                'route' => null,
                'rate_3t' => null,
                'rate_5t' => null,
                'rate_7t' => null,
                'rate_10t' => null,
            ],
        ]);
    }
}

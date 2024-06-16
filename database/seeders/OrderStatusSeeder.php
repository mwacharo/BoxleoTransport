<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Processing',
            'Ready for Pickup',
            'Picked Up',
            'In Transit',
            'Out for Delivery',
            'Delivered',
            'Failed Delivery',
            'Returned',
            'Cancelled',
            'On Hold',
            'Rescheduled',
        ];

        foreach ($statuses as $status) {
            OrderStatus::create([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

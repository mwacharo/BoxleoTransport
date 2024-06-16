<?php

namespace Database\Seeders;

use App\Models\OrderCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $orderCategories = [
            ['name' => 'Point-to-Point Collections', 'description' => 'Picking up goods from a specified location (Point A) and delivering them to another location (Point B).'],
            ['name' => 'Multi-Stop Collections', 'description' => 'Collecting goods from multiple locations and delivering them to one or more destinations.'],
            ['name' => 'Direct Delivery', 'description' => 'Delivering goods directly from a warehouse or supplier to the client.'],
            ['name' => 'Scheduled Delivery', 'description' => 'Delivering goods at a specific scheduled time and date.'],
            ['name' => 'Express Delivery', 'description' => 'Priority delivery requiring faster processing and transit times.'],
            ['name' => 'Warehouse Pickup', 'description' => 'Clients picking up goods directly from the warehouse.'],
            ['name' => 'Warehouse Transfer', 'description' => 'Moving goods between different warehouses or storage facilities.'],
            ['name' => 'Dedicated Truck', 'description' => 'Full truckload shipments where a single client occupies the entire truck.'],
            ['name' => 'Partial Truckload', 'description' => 'Shared truck space between multiple clients, but with larger shipment volumes than LTL.'],
            ['name' => 'Consolidated Shipments', 'description' => 'Smaller shipments from multiple clients consolidated into one truck.'],
            ['name' => 'Shared Truck', 'description' => 'Multiple clients share the truck space for their smaller volume shipments.'],
            ['name' => 'Returns Management', 'description' => 'Handling returns from clients back to the warehouse or supplier.'],
            ['name' => 'Recycling and Disposal', 'description' => 'Collecting goods for recycling or proper disposal.'],
            ['name' => 'Inbound Cross-Docking', 'description' => 'Receiving goods from suppliers and directly transferring them to outbound trucks without long-term storage.'],
            ['name' => 'Outbound Cross-Docking', 'description' => 'Consolidating products from different suppliers or warehouses for immediate distribution.'],
            ['name' => 'Supplier Direct Delivery', 'description' => 'Orders where goods are shipped directly from the supplier to the end customer, bypassing the logistics company\'s warehouse.'],
            ['name' => 'Home Delivery', 'description' => 'Delivering online orders directly to customers\' homes.'],
            ['name' => 'Click and Collect', 'description' => 'Customers order online and pick up their goods from a designated location.'],
            ['name' => 'Perishable Goods', 'description' => 'Orders requiring temperature control and expedited delivery.'],
            ['name' => 'Hazardous Materials', 'description' => 'Orders involving dangerous goods requiring special handling and compliance with safety regulations.'],
            ['name' => 'High-Value Goods', 'description' => 'Orders involving expensive items requiring enhanced security measures.'],
            ['name' => 'Export Orders', 'description' => 'Shipping goods to international destinations.'],
            ['name' => 'Import Orders', 'description' => 'Receiving goods from international suppliers.'],
            ['name' => 'Rail and Road', 'description' => 'Combining rail and road transport for optimal logistics solutions.'],
            ['name' => 'Sea and Road', 'description' => 'Combining sea freight with road transport for international shipments.']
        ];

        foreach ($orderCategories as $category) {
            OrderCategory::create($category);
        }
    }
}

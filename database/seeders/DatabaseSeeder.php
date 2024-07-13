<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// use App\Models\OrderCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            BranchesTableSeeder::class,
            OrderStatusSeeder::class,
            OrderCategorySeeder::class,
            VendorSeeder::class,
            WarehouseSeeder::class,
            RowSeeder::class,
            AreaSeeder::class,
            BaySeeder::class,
            LevelSeeder::class,
            BinSeeder::class,
            DriverSeeder::class,
            ClientSeeder::class,
            RolePermissionSeeder::class,
            // MarketSeeder::class,
            // OrderSeeder::class,
            // OrderProductSeeder::class,
            // ProductInstanceSeeder::class,
            // ProductSeeder::class,
            // RiderSeeder::class,
            // SheetSeeder::class,
         



        ]);
    }
}

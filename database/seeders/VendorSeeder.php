<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Vendor;

use Carbon\Carbon;

class VendorSeeder extends Seeder
{
  public function run(): void

    {

        $vendors = [

            [

                'name' => 'Acme Inc.',

                'phone' => '1234567890',

                'address' => '123 Main St',

                'email' => 'acme@example.com',

                'email_verified_at' => now(),

                 'password' => bcrypt('password'),

                'active' => true,

                'sheet_update' => true,

                'send_sms' => true,

                'telegram_notifications' => true,

                'shopify_email' => 'acme@example.com',

                'order_no_start' => 1,

                'order_no_end' => 100,

                'autogenerate' => true,

                'portal_active' => true,

                'terms' => 'Terms and Conditions',

                'company_id' => 1,

                'branch_id' => 1,

                'order_prefix' => 'ACME-',

                // 'remember_token' => str_random(10),

                'created_at' => Carbon::now(),

                'updated_at' => Carbon::now(),

                // 'date' => Carbon::now(),

                'last_identifier' => 1,

            ],

            [

                'name' => 'Best Buy',

                'phone' => '0987654321',

                'address' => '456 Elm St',

                'email' => 'bestbuy@example.com',

                'email_verified_at' => now(),

                'password' => bcrypt('password'),

                'active' => true,

                'sheet_update' => true,

                'send_sms' => true,

                'telegram_notifications' => true,

                'shopify_email' => 'bestbuy@example.com',

                'order_no_start' => 1,

                'order_no_end' => 100,

                'autogenerate' => true,

                'portal_active' => true,

                'terms' => 'Terms and Conditions',

                'company_id' => 2,

                'branch_id' => 2,

                'order_prefix' => 'BB-',

                // 'remember_token' => str_random(10),

                'created_at' => Carbon::now(),

                'updated_at' => Carbon::now(),

                // 'date' => Carbon::now(),

                'last_identifier' => 2,

            ],

            // Add more vendors as needed

        ];


        foreach ($vendors as $vendor) {

            Vendor::create($vendor);

        }

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Define roles
        $superAdminRole = Role::create(['name' => 'superadmin']);
        $adminRole = Role::create(['name' => 'admin']);
        $vendorRole = Role::create(['name' => 'vendor']);
        $vendorUserRole = Role::create(['name' => 'vendor-user']);
        $driverRole = Role::create(['name' => 'driver']);
        $riderRole = Role::create(['name' => 'rider']);
        $userRole = Role::create(['name' => 'user']);

        // Define permissions
        $permissions = [
            'view dashboard',
            'manage orders',
            'manage order status',
            'manage order categories',
            'dispatch orders',
            'manage vendors',
            'manage clients',
            'manage fleet',
            'manage zones',
            'manage inventory',
            'manage warehouses',
            'view reports',
            'manage users',
            'manage user roles',
            'view own dashboard',
            'manage own orders',
            'view own inventory',
            'create orders',
            'edit own orders',
            'import orders',
            'track orders in real-time',
            'view assigned orders',
            'view routes',
            'update order status',
            'manage products',  
            'view products',    
            'manage product instances',  
            'view product instances',   
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $superAdminRole->givePermissionTo(Permission::all());
        $adminRole->givePermissionTo(Permission::all());

        $vendorPermissions = [
            'view own dashboard',
            'manage own orders',
            'view own inventory',
            'create orders',
            'edit own orders',
            'import orders',
            'track orders in real-time',
            'manage products',
            'view products',
            'manage product instances',
            'view product instances',
        ];
        $vendorRole->givePermissionTo($vendorPermissions);
        $vendorUserRole->givePermissionTo($vendorPermissions);

        $driverPermissions = [
            'view assigned orders',
            'view routes',
            'update order status',
        ];
        $driverRole->givePermissionTo($driverPermissions);
        $riderRole->givePermissionTo($driverPermissions);
    }
}

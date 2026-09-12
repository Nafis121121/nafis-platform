<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'super_admin@nafis.com', 'role' => 'super_admin'],
            ['name' => 'Sales Manager', 'email' => 'sales@nafis.com', 'role' => 'sales_manager'],
            ['name' => 'Warehouse Staff', 'email' => 'warehouse@nafis.com', 'role' => 'warehouse_staff'],
            ['name' => 'Demo Customer', 'email' => 'customer@nafis.com', 'role' => 'customer'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}

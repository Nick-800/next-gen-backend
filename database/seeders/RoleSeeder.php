<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin    = Role::firstOrCreate(['name' => 'admin',    'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Create a default admin user (idempotent)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@nextgen.test'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('password'),
            ]
        );

        $adminUser->assignRole($admin);

        $this->command->info("✅ Roles created: admin, customer");
        $this->command->info("✅ Admin user: admin@nextgen.test / password");
    }
}

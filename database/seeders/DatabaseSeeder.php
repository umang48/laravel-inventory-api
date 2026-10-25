<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Create the Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 2. Create a test user
        $user = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Assign the role
        $user->assignRole($adminRole);

        // Generate 50 random products
        Product::factory(50)->create();
    }
}

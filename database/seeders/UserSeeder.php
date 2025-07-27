<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates production-ready users for development:
     * - 1 Administrator
     * - 2 Managers
     * - 2 Surveyors
     */
    public function run(): void
    {
        // Clear existing users to avoid conflicts
        User::truncate();

        // 1. Administrator
        $admin = User::create([
            'id' => Str::uuid(),
            'name' => 'Administrator',
            'email' => 'admin@realestate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'remember_token' => Str::random(10),
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(30),
        ]);

        // 2. Manager 1
        $manager1 = User::create([
            'id' => Str::uuid(),
            'name' => 'Manajer Aset Jakarta',
            'email' => 'manager1@realestate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'remember_token' => Str::random(10),
            'created_at' => now()->subDays(25),
            'updated_at' => now()->subDays(25),
        ]);

        // 3. Manager 2
        $manager2 = User::create([
            'id' => Str::uuid(),
            'name' => 'Manajer Aset Bandung',
            'email' => 'manager2@realestate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('manager123'),
            'role' => 'manager',
            'remember_token' => Str::random(10),
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        // 4. Surveyor 1
        $surveyor1 = User::create([
            'id' => Str::uuid(),
            'name' => 'Surveyor Lapangan A',
            'email' => 'surveyor1@realestate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('surveyor123'),
            'role' => 'surveyor',
            'remember_token' => Str::random(10),
            'created_at' => now()->subDays(15),
            'updated_at' => now()->subDays(15),
        ]);

        // 5. Surveyor 2
        $surveyor2 = User::create([
            'id' => Str::uuid(),
            'name' => 'Surveyor Lapangan B',
            'email' => 'surveyor2@realestate.com',
            'email_verified_at' => now(),
            'password' => Hash::make('surveyor123'),
            'role' => 'surveyor',
            'remember_token' => Str::random(10),
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $this->command->info("✅ Created 5 users:");
        $this->command->line("   - 1 Admin: {$admin->email}");
        $this->command->line("   - 2 Managers: {$manager1->email}, {$manager2->email}");
        $this->command->line("   - 2 Surveyors: {$surveyor1->email}, {$surveyor2->email}");
    }
}

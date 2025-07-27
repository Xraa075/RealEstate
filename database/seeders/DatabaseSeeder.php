<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for production-ready development.
     *
     * This seeder creates:
     * - 1 Admin, 2 Managers, 2 Surveyors
     * - 4 Approved Assets (2 by admin, 2 by manager)
     * - 4 Pending Asset Requests (2 per surveyor)
     * - Activity logs for all actions
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Real Estate Database Seeding...');

        // Users first (required for foreign keys)
        $this->command->info('👥 Creating users...');
        $this->call(UserSeeder::class);

        // Assets (approved ones)
        $this->command->info('🏠 Creating approved land assets...');
        $this->call(LandAssetSeeder::class);

        // Pending requests
        $this->command->info('📝 Creating pending asset requests...');
        $this->call(AssetRequestSeeder::class);

        // Activity logs
        $this->command->info('📊 Creating activity logs...');
        $this->call(ActivityLogSeeder::class);        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->line('');
        $this->command->line('🔑 Login Credentials:');
        $this->command->line('Admin: admin@realestate.com / admin123');
        $this->command->line('Manager 1: manager1@realestate.com / manager123');
        $this->command->line('Manager 2: manager2@realestate.com / manager123');
        $this->command->line('Surveyor 1: surveyor1@realestate.com / surveyor123');
        $this->command->line('Surveyor 2: surveyor2@realestate.com / surveyor123');
    }
}

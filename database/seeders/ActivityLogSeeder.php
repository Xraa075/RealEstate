<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\LandAsset;
use App\Models\AssetRequest;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates basic activity logs for demonstration:
     * - User login/logout activities
     * - Asset viewing activities
     * - Request submission activities
     */
    public function run(): void
    {
        // Clear existing logs
        ActivityLog::truncate();

        // Get users
        $users = User::all();
        $assets = LandAsset::take(5)->get();
        $requests = AssetRequest::take(3)->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ No users found. Please run UserSeeder first.');
            return;
        }

        $activities = [];

        // Generate login activities for each user
        foreach ($users as $index => $user) {
            $activities[] = [
                'user_id' => $user->id,
                'activity_type' => 'login',
                'description' => "User {$user->name} ({$user->role}) logged into the system",
                'ip_address' => '192.168.1.' . (100 + $index),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(rand(1, 30)),
            ];
        }

        // Generate asset viewing activities
        if ($assets->isNotEmpty()) {
            foreach ($users->take(3) as $user) {
                $asset = $assets->random();
                $activities[] = [
                    'user_id' => $user->id,
                    'activity_type' => 'view_asset',
                    'description' => "User {$user->name} viewed asset: {$asset->name} ({$asset->asset_code})",
                    'ip_address' => '192.168.1.' . rand(100, 200),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => now()->subDays(rand(1, 15)),
                ];
            }
        }

        // Generate request submission activities
        if ($requests->isNotEmpty()) {
            $surveyors = $users->where('role', 'surveyor');
            foreach ($requests as $request) {
                $surveyor = $surveyors->where('id', $request->requested_by)->first();
                if ($surveyor) {
                    $requestData = is_string($request->proposed_data)
                        ? json_decode($request->proposed_data, true)
                        : $request->proposed_data;
                    $activities[] = [
                        'user_id' => $surveyor->id,
                        'activity_type' => 'submit_request',
                        'description' => "User {$surveyor->name} submitted new asset request: {$requestData['name']}",
                        'ip_address' => '192.168.1.' . rand(100, 200),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'created_at' => $request->created_at,
                    ];
                }
            }
        }

        // Create activity logs
        foreach ($activities as $activity) {
            ActivityLog::create($activity);
        }

        $this->command->info("✅ Created " . count($activities) . " activity log entries");
        $this->command->line("   - Login activities for all users");
        $this->command->line("   - Asset viewing activities");
        $this->command->line("   - Request submission activities");
    }
}

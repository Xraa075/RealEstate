<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetRequest;
use App\Models\User;
use Illuminate\Support\Str;

class AssetRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 4 pending asset requests:
     * - 2 requests from Surveyor 1
     * - 2 requests from Surveyor 2
     */
    public function run(): void
    {
        // Clear existing requests
        AssetRequest::truncate();

        // Get surveyors
        $surveyors = User::where('role', 'surveyor')->take(2)->get();

        if ($surveyors->count() < 2) {
            $this->command->error('❌ Required surveyors not found. Please run UserSeeder first.');
            return;
        }

        $surveyor1 = $surveyors->first();
        $surveyor2 = $surveyors->last();

        // Create 4 pending requests (2 each surveyor)
        for ($i = 1; $i <= 4; $i++) {
            $surveyor = $i <= 2 ? $surveyor1 : $surveyor2;

            $proposedData = [
                'asset_code' => 'TNH-REQ-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Tanah Request ' . $i,
                'description' => 'Deskripsi tanah request nomor ' . $i,
                'area_sqm' => rand(1000, 5000),
                'address' => 'Alamat Request ' . $i . ', Jakarta',
                'status' => 'tersedia',
                'value' => rand(1000000000, 10000000000),
                'geometry' => '{"type":"Polygon","coordinates":[[[106.8,' . (-6.2 - $i*0.01) . '],[106.81,' . (-6.2 - $i*0.01) . '],[106.81,' . (-6.19 - $i*0.01) . '],[106.8,' . (-6.19 - $i*0.01) . '],[106.8,' . (-6.2 - $i*0.01) . ']]]}',
                'owner_name' => 'Owner ' . $i,
                'owner_contact' => '+62-21-555-' . str_pad($i, 4, '0', STR_PAD_LEFT),
            ];

            AssetRequest::create([
                'id' => Str::uuid(),
                'type' => 'create',
                'asset_id' => null,
                'proposed_data' => json_encode($proposedData),
                'requested_by' => $surveyor->id,
                'approved_by' => null,
                'status' => 'pending',
                'notes' => 'Request ' . $i . ' oleh ' . $surveyor->name,
                'reviewed_at' => null,
                'created_at' => now()->subDays(20 - $i),
                'updated_at' => now()->subDays(20 - $i),
            ]);
        }

        $this->command->info("✅ Created 4 pending asset requests:");
        $this->command->line("   - 2 requests from {$surveyor1->name}");
        $this->command->line("   - 2 requests from {$surveyor2->name}");
        $this->command->line("   - All requests are pending approval");
    }
}

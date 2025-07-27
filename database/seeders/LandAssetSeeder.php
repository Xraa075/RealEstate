<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandAsset;
use App\Models\User;
use Illuminate\Support\Str;

class LandAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 4 approved land assets:
     * - 2 approved by Admin
     * - 2 approved by Manager
     */
    public function run(): void
    {
        // Clear existing assets
        LandAsset::truncate();

        // Get users for created_by field
        $admin = User::where('role', 'admin')->first();
        $managers = User::where('role', 'manager')->take(2)->get();

        if (!$admin || $managers->count() < 2) {
            $this->command->error('❌ Required users not found. Please run UserSeeder first.');
            return;
        }

        // Sample geometries for different Jakarta areas
        $geometries = [
            '{"type":"Polygon","coordinates":[[[106.8243,-6.2601],[106.8343,-6.2601],[106.8343,-6.2501],[106.8243,-6.2501],[106.8243,-6.2601]]]}', // Jakarta Selatan - Cilandak
            '{"type":"Polygon","coordinates":[[[106.7543,-6.1801],[106.7643,-6.1801],[106.7643,-6.1701],[106.7543,-6.1701],[106.7543,-6.1801]]]}', // Jakarta Barat - Grogol
            '{"type":"Polygon","coordinates":[[[106.7943,-6.2401],[106.8043,-6.2401],[106.8043,-6.2301],[106.7943,-6.2301],[106.7943,-6.2401]]]}', // Jakarta Selatan - Pondok Indah
            '{"type":"Polygon","coordinates":[[[106.9143,-6.2101],[106.9243,-6.2101],[106.9243,-6.2001],[106.9143,-6.2001],[106.9143,-6.2101]]]}', // Jakarta Timur - Klender
        ];

        // 4 Land Assets Data
        $assetsData = [
            // Assets approved by Admin (2)
            [
                'asset_code' => 'TNH-001-JKS',
                'name' => 'Tanah Kavling Cilandak Premium',
                'description' => 'Tanah kavling premium di area Cilandak dengan akses jalan utama. Cocok untuk pembangunan perumahan mewah.',
                'area_sqm' => 2500.00,
                'address' => 'Jl. Cilandak Raya No. 45, Cilandak, Jakarta Selatan',
                'status' => 'tersedia',
                'value' => 7500000000.00, // 7.5 Miliar
                'geometry' => $geometries[0],
                'owner_name' => 'PT. Properti Cilandak Indah',
                'owner_contact' => '+62-21-7590001',
                'created_by' => $admin->id,
            ],
            [
                'asset_code' => 'TNH-002-JKB',
                'name' => 'Tanah Komersial Grogol',
                'description' => 'Tanah strategis untuk pembangunan pusat perbelanjaan di area Grogol.',
                'area_sqm' => 5000.00,
                'address' => 'Jl. Grogol Raya No. 128, Grogol Petamburan, Jakarta Barat',
                'status' => 'tersedia',
                'value' => 15000000000.00, // 15 Miliar
                'geometry' => $geometries[1],
                'owner_name' => 'CV. Grogol Property',
                'owner_contact' => '+62-21-5680002',
                'created_by' => $admin->id,
            ],

            // Assets approved by Manager (2)
            [
                'asset_code' => 'TNH-003-JKS',
                'name' => 'Tanah Perumahan Pondok Indah',
                'description' => 'Tanah untuk cluster perumahan elite di kawasan Pondok Indah.',
                'area_sqm' => 4000.00,
                'address' => 'Jl. Metro Pondok Indah, Pondok Indah, Jakarta Selatan',
                'status' => 'tersedia',
                'value' => 16000000000.00, // 16 Miliar
                'geometry' => $geometries[2], // Koordinat khusus untuk Pondok Indah
                'owner_name' => 'PT. Pondok Indah Residence',
                'owner_contact' => '+62-21-7506006',
                'created_by' => $managers->first()->id,
            ],
            [
                'asset_code' => 'TNH-004-JKT',
                'name' => 'Tanah Ruko Klender',
                'description' => 'Tanah untuk pembangunan ruko di kawasan ramai Klender.',
                'area_sqm' => 1800.00,
                'address' => 'Jl. Raya Klender No. 45, Klender, Jakarta Timur',
                'status' => 'tersedia',
                'value' => 5400000000.00, // 5.4 Miliar
                'geometry' => $geometries[3], // Koordinat khusus untuk Klender
                'owner_name' => 'CV. Klender Sejahtera',
                'owner_contact' => '+62-21-8609009',
                'created_by' => $managers->first()->id,
            ],
        ];

        // Create assets with proper timestamps
        foreach ($assetsData as $index => $assetData) {
            $asset = LandAsset::create([
                'id' => Str::uuid(),
                'asset_code' => $assetData['asset_code'],
                'name' => $assetData['name'],
                'description' => $assetData['description'],
                'area_sqm' => $assetData['area_sqm'],
                'address' => $assetData['address'],
                'status' => $assetData['status'],
                'value' => $assetData['value'],
                'geometry' => $assetData['geometry'],
                'owner_name' => $assetData['owner_name'],
                'owner_contact' => $assetData['owner_contact'],
                'created_by' => $assetData['created_by'],
                'updated_by' => $assetData['created_by'],
                'created_at' => now()->subDays(30 - $index * 2), // Staggered creation dates
                'updated_at' => now()->subDays(30 - $index * 2),
            ]);
        }

        $this->command->info("✅ Created 4 land assets:");
        $this->command->line("   - 2 approved by Admin");
        $this->command->line("   - 2 approved by Manager ({$managers->first()->name})");
    }
}

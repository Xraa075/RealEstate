<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('land_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('area_sqm', 10, 2);
            $table->text('address');
            $table->string('status', 50)->default('tersedia');
            $table->decimal('value', 15, 2);
            // Menyimpan data geometri sebagai JSON
            $table->json('geometry')->nullable();
            $table->string('owner_name');
            $table->string('owner_contact');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('land_assets');
    }
};

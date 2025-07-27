<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('land_assets', function (Blueprint $table) {
            // Increase precision for area_sqm from decimal(10,2) to decimal(15,2)
            $table->decimal('area_sqm', 15, 2)->change();

            // Increase precision for value from decimal(15,2) to decimal(20,2)
            $table->decimal('value', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('land_assets', function (Blueprint $table) {
            // Revert back to original precision
            $table->decimal('area_sqm', 10, 2)->change();
            $table->decimal('value', 15, 2)->change();
        });
    }
};

<?php
/**
 * Migration: Create Activities Table
 * Purpose: Stores activities (Rafting, Paragliding, Jungle Safari) - many-to-many with packages.
 *          Filament: ActivityResource, selectable as tags in Package.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Activity name');
            $table->string('slug')->unique()->comment('Slug');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // NOTE: Pivot table activity_package created later after packages table (see 2026_08_22_070013_create_pivots_table.php)
    }
    public function down(): void { Schema::dropIfExists('activities'); }
};

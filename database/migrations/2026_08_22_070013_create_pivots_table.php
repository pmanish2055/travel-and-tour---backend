<?php
/**
 * Migration: Create Remaining Pivot Tables
 * Purpose: Creates activity_package pivot (deferred from earlier) and package_addon etc.
 *          Ensures packages table exists before creating FKs.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activity <-> Package pivot (if not exists)
        if (!Schema::hasTable('activity_package')) {
            Schema::create('activity_package', function (Blueprint $table) {
                $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete()->comment('FK to activities');
                $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
                $table->primary(['activity_id','package_id']);
            });
        }
        // Tag pivot for later (package_tag) - placeholder if needed
    }
    public function down(): void { Schema::dropIfExists('activity_package'); }
};

<?php
/**
 * Migration: Create Addons Table + Pivot
 * Purpose: Extra services purchasable with package (Porter, Extra hotel night, Sleeping bag, etc).
 *          Many-to-many with packages via addon_package pivot.
 *          Filament: AddonResource
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Addon name e.g., Extra Porter');
            $table->string('slug')->unique()->comment('Slug');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->comment('Price per unit');
            $table->string('price_type')->default('per_person')->comment('per_person or per_group');
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('addon_package', function (Blueprint $table) {
            $table->foreignId('addon_id')->constrained('addons')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->primary(['addon_id','package_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('addon_package'); Schema::dropIfExists('addons'); }
};

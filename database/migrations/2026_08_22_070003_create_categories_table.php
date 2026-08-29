<?php
/**
 * Migration: Create Categories Table
 * Purpose: Stores tour categories (Trekking, Hiking, Peak Climbing, Cultural Tour, etc).
 *          Packages belong to one category. Used for filtering and navigation.
 *          Filament Resource: CategoryResource
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // PK
            $table->string('name')->unique()->comment('Category name e.g., Trekking');
            $table->string('slug')->unique()->comment('Slug for /activities/{slug}');
            $table->text('description')->nullable()->comment('Category description for SEO/category page');
            $table->string('icon')->nullable()->comment('Heroicon name or icon class');
            $table->string('color', 20)->nullable()->comment('Hex color for badge');
            $table->string('featured_image')->nullable()->comment('Category header image');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); }
};

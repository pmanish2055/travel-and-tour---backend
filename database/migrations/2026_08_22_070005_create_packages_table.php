<?php
/**
 * Migration: Create Packages Table (CENTRAL ENTITY)
 * Purpose: Core product for Nepal tours/treks. Each package is a sellable tour with pricing,
 *          duration, altitude, difficulty, etc. Related to Category, Destination, Region.
 *          Used by: PackageResource (Filament), Api\PackageController, Booking, Inquiry
 *          Related Models: Category, Destination, Region, Itinerary, Inclusion, Faq, Departure, Booking
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id()->comment('PK - unique package id');

            // Relations - foreign keys
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->comment('FK to categories - package category');
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete()->comment('FK to destinations - primary destination');
            $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete()->comment('FK to regions - denormalized for quick filtering');

            // Basic info
            $table->string('title')->comment('Package title e.g., Everest Base Camp Trek 14 Days');
            $table->string('slug')->unique()->comment('Unique slug for /packages/{slug}');
            $table->string('short_description', 500)->nullable()->comment('Short excerpt for listing cards');
            $table->longText('overview')->nullable()->comment('Detailed overview HTML/rich text');
            $table->json('highlights')->nullable()->comment('JSON array of highlights bullet points');

            // Trip specifics - Nepal critical
            $table->smallInteger('duration_days')->comment('Total days');
            $table->smallInteger('duration_nights')->comment('Total nights');
            $table->smallInteger('group_size_min')->default(2)->comment('Min group size');
            $table->smallInteger('group_size_max')->default(16)->comment('Max group size');
            $table->integer('max_altitude_m')->nullable()->comment('Max altitude in meters');
            $table->enum('difficulty', ['easy','moderate','hard','strenuous','challenging'])->default('moderate')->comment('Difficulty level');
            $table->json('best_season')->nullable()->comment('JSON e.g., ["Spring","Autumn"]');
            $table->string('accommodation')->nullable()->comment('Teahouse/Hotel/Camping');
            $table->string('meal_plan')->nullable()->comment('B/L/D meal plan');
            $table->string('transportation')->nullable()->comment('Transport details');
            $table->enum('trip_type', ['fixed_departure','private','daily'])->default('private')->comment('Departure type');

            // Pricing
            $table->decimal('price', 10, 2)->comment('Base price');
            $table->decimal('discount_price', 10, 2)->nullable()->comment('Discounted price if on sale');
            $table->enum('price_type', ['per_person','per_group'])->default('per_person');
            $table->enum('currency', ['NPR','USD'])->default('NPR');
            $table->boolean('is_price_on_request')->default(false)->comment('Hide price, show on request');

            // Status & visibility
            $table->boolean('featured')->default(false)->comment('Featured on homepage');
            $table->boolean('is_trending')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->enum('status', ['draft','published','archived'])->default('draft')->comment('Publish status');
            $table->timestamp('published_at')->nullable()->comment('When published');

            // Media
            $table->string('featured_image')->nullable()->comment('Main image path - also via media library');

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();

            // Stats
            $table->unsignedInteger('view_count')->default(0)->comment('View counter for popularity');

            // Ordering
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes for filtering performance
            $table->index('category_id');
            $table->index('destination_id');
            $table->index('region_id');
            $table->index('difficulty');
            $table->index('status');
            $table->index('price');
            $table->index('featured');
            $table->index('published_at');
        });
    }
    public function down(): void { Schema::dropIfExists('packages'); }
};

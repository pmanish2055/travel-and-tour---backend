<?php
/**
 * Migration: Create Tags + Package Pricings + Hide Separate Menus Support
 * Purpose: Adds tags for SEO (product tags) and package pricings (single/group)
 *          Tags are used for SEO and filtering. Pricings handle single vs group pricing as requested.
 *          After this, Package will have tabs: General, Itinerary, Includes/Excludes, Departures, Pricing, SEO/Tags
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // === 1. Tags table: for SEO product tags (e.g., "family", "adventure", "luxury", "budget", "ebc", "annapurna") ===
        Schema::create('tags', function (Blueprint $table) {
            $table->id()->comment('PK for tag');
            $table->string('name')->unique()->comment('Tag name e.g., Adventure, Family, Luxury');
            $table->string('slug')->unique()->comment('Slug for URL/filtering e.g., adventure');
            $table->string('color', 20)->nullable()->comment('Badge color hex');
            $table->text('description')->nullable()->comment('Description for SEO');
            $table->boolean('is_active')->default(true)->comment('Active flag');
            $table->timestamps();
        });

        // Pivot: package_tag (many-to-many)
        Schema::create('package_tag', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete()->comment('FK to tags');
            $table->primary(['package_id', 'tag_id']);
        });

        // === 2. Package Pricings: handles Single vs Group pricing per package ===
        // Each package can have multiple pricing tiers: e.g., Single traveler $1500, Group 2-4 $1200pp, Group 5-8 $1000pp
        Schema::create('package_pricings', function (Blueprint $table) {
            $table->id()->comment('PK for pricing tier');
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->string('title')->comment('Title e.g., Single Traveler, Group 2-4 Pax, Group 5-8 Pax');
            $table->enum('type', ['single', 'group', 'private', 'fixed'])->default('group')->comment('Pricing type: single=1 pax, group=2+ pax, private, fixed departure');
            $table->smallInteger('pax_min')->default(1)->comment('Min pax for this tier');
            $table->smallInteger('pax_max')->nullable()->comment('Max pax (null = unlimited)');
            $table->decimal('price_per_person', 10, 2)->comment('Price per person for this tier');
            $table->decimal('total_price', 10, 2)->nullable()->comment('Optional total group price (if per_group)');
            $table->string('currency', 3)->default('USD')->comment('Currency NPR/USD');
            $table->text('description')->nullable()->comment('Description e.g., Includes...');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['package_id', 'type']);
        });

        // === 3. Add SEO tags column to packages for quick filtering (optional denormalized) ===
        // We keep pivot as main, but also add seo_keywords already exists. No new column needed.
        // Ensure packages has is_trending etc already.
    }

    public function down(): void
    {
        Schema::dropIfExists('package_pricings');
        Schema::dropIfExists('package_tag');
        Schema::dropIfExists('tags');
    }
};

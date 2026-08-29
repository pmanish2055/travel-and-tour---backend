<?php
/**
 * Migration: Create Regions Table
 * Purpose: Stores hierarchical regions for Nepal (Everest, Annapurna, Langtang, etc).
 *          Used to group destinations. Supports parent_id for sub-regions.
 *          Filament Resource: RegionResource (Tour Management group)
 *          Related Models: Destination, Package
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates regions table with hierarchical support and SEO fields.
     */
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            // Primary key
            $table->id(); // unsignedBigInteger, autoIncrement - unique identifier for region

            // Hierarchical support: nullable parent_id for sub-regions (e.g., Khumbu under Everest)
            $table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete()->comment('Parent region for hierarchy');

            // Core fields
            $table->string('name')->comment('Region name e.g., Everest Region'); // indexed via unique slug
            $table->string('slug')->unique()->comment('URL friendly slug, auto-generated from name');
            $table->text('description')->nullable()->comment('Overview of region for frontend display');

            // Media & display
            $table->string('featured_image')->nullable()->comment('Main image path for region card');
            $table->boolean('is_featured')->default(false)->comment('Show on homepage featured regions');
            $table->boolean('is_active')->default(true)->comment('Active flag to hide/show without delete');

            // Ordering & SEO
            $table->integer('sort_order')->default(0)->comment('Manual sort order for listing');
            $table->string('seo_title')->nullable()->comment('SEO meta title');
            $table->text('seo_description')->nullable()->comment('SEO meta description');

            // Timestamps and soft delete for safe removal
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at for soft delete

            // Indexes for performance
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     * Drops regions table.
     */
    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};

<?php
/**
 * Migration: Create Destinations Table
 * Purpose: Stores specific destinations within regions (e.g., Poon Hill, EBC, Phewa Lake).
 *          Each destination belongs to a region. Used to filter packages.
 *          Filament Resource: DestinationResource
 *          Related: Region (belongsTo), Package (hasMany)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates destinations table with geo and SEO.
     */
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id(); // PK

            // Foreign key to regions - required, cascade on delete
            $table->foreignId('region_id')->constrained('regions')->cascadeOnDelete()->comment('FK to regions.id - which region this destination belongs to');

            // Core fields
            $table->string('name')->comment('Destination name e.g., Ghorepani Poon Hill');
            $table->string('slug')->unique()->comment('Unique slug for frontend URL /destinations/{slug}');
            $table->text('overview')->nullable()->comment('Long description for destination page');
            $table->string('short_description', 500)->nullable()->comment('Short excerpt for cards');

            // Geo & altitude - Nepal specific critical info
            $table->integer('altitude_m')->nullable()->comment('Max altitude in meters for this destination');
            $table->decimal('latitude', 10, 7)->nullable()->comment('GPS latitude for map embed');
            $table->decimal('longitude', 10, 7)->nullable()->comment('GPS longitude for map embed');

            // Display & media
            $table->string('featured_image')->nullable()->comment('Featured image path');
            $table->json('best_season')->nullable()->comment('JSON array e.g., ["Spring","Autumn"]');
            $table->boolean('is_featured')->default(false)->comment('Show on homepage');
            $table->boolean('is_active')->default(true);

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('region_id');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};

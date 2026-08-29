<?php
/**
 * Migration: Add video and map to packages and destinations + polish missing travel features
 * Purpose: Adds video_url and map_embed to packages/destinations for even better UI as you requested.
 *          Also adds gallery JSON to destinations, and ensures packages have video for YouTube embeds.
 *          Makes backend even more good for travel site.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Packages: add video and map
        Schema::table('packages', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('featured_image')->comment('YouTube/Vimeo URL for package video, e.g., https://youtube.com/watch?v=...');
            $table->text('map_embed')->nullable()->after('video_url')->comment('Google Map embed iframe or URL for package location');
            $table->json('gallery')->nullable()->after('map_embed')->comment('JSON gallery images if not using Spatie Media Library');
        });

        // Destinations: add gallery and video
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('featured_image')->comment('Destination video URL');
            $table->json('gallery')->nullable()->after('video_url')->comment('Gallery JSON for destination images');
            $table->text('map_embed')->nullable()->after('gallery')->comment('Map embed for destination');
        });

        // Regions: add gallery
        Schema::table('regions', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('featured_image')->comment('Gallery for region');
        });

        // Blog posts: add video
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('featured_image')->comment('Blog video URL');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'map_embed', 'gallery']);
        });
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['video_url', 'gallery', 'map_embed']);
        });
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('gallery');
        });
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};

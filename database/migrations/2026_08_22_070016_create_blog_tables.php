<?php
/**
 * Migration: Create Blog Tables
 * Purpose: Blog system for SEO and content marketing. Categories, Tags, Posts.
 *          Posts have rich content, featured image, SEO, author (user).
 *          Filament: BlogPostResource, etc.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Category name e.g., Travel Tips');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Post title');
            $table->string('slug')->unique()->comment('URL slug /blogs/{slug}');
            $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete()->comment('FK to category');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Author user');
            $table->string('excerpt', 500)->nullable()->comment('Short excerpt for listing');
            $table->longText('content')->comment('Rich HTML content');
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft','published','archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('blog_category_id');
            $table->index('status');
            $table->index('is_featured');
        });
        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained('blog_posts')->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->primary(['blog_post_id','blog_tag_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('blog_post_tag'); Schema::dropIfExists('blog_posts'); Schema::dropIfExists('blog_tags'); Schema::dropIfExists('blog_categories'); }
};

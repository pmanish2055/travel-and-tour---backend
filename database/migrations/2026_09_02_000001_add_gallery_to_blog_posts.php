<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('blog_posts')) return;
        if (!Schema::hasColumn('blog_posts', 'gallery')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->json('gallery')->nullable()->after('featured_image')->comment('Gallery JSON images');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blog_posts') && Schema::hasColumn('blog_posts', 'gallery')) {
            Schema::table('blog_posts', function (Blueprint $table) {
                $table->dropColumn('gallery');
            });
        }
    }
};

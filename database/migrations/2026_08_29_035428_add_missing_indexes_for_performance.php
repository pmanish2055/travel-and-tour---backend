<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            try { $table->index('user_id'); } catch (\Throwable $e) {}
            try { $table->index('customer_email'); } catch (\Throwable $e) {}
            try { $table->index('departure_id'); } catch (\Throwable $e) {}
            try { $table->index(['booking_status','travel_date']); } catch (\Throwable $e) {}
        });
        Schema::table('packages', function (Blueprint $table) {
            try { $table->index('view_count'); } catch (\Throwable $e) {}
            try { $table->index(['status','featured']); } catch (\Throwable $e) {}
            try { $table->index(['status','is_trending']); } catch (\Throwable $e) {}
            try { $table->index(['status','is_popular']); } catch (\Throwable $e) {}
        });
        // settings group+key for fast lookups
        Schema::table('settings', function (Blueprint $table) {
            try { $table->index(['group','key']); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            try { $table->dropIndex(['user_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['customer_email']); } catch (\Throwable $e) {}
            try { $table->dropIndex(['departure_id']); } catch (\Throwable $e) {}
            try { $table->dropIndex('bookings_booking_status_travel_date_index'); } catch (\Throwable $e) {}
        });
        Schema::table('packages', function (Blueprint $table) {
            try { $table->dropIndex(['view_count']); } catch (\Throwable $e) {}
            try { $table->dropIndex('packages_status_featured_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('packages_status_is_trending_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('packages_status_is_popular_index'); } catch (\Throwable $e) {}
        });
        Schema::table('settings', function (Blueprint $table) {
            try { $table->dropIndex('settings_group_key_index'); } catch (\Throwable $e) {}
        });
    }
};

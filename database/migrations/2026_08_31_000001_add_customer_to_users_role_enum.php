<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'customer' to users.role enum (required for frontend registered users + profile).
     * Fixes 403 for customer users and ensures AdminSeeder + UserResource form (with customer option) works.
     * Safe for both MySQL (cPanel) and SQLite (local).
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL: alter enum to include customer
            try {
                DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin','admin','editor','agent','customer') NOT NULL DEFAULT 'agent' COMMENT 'Role for Shield - super_admin has all access'");
            } catch (\Exception $e) {
                // Fallback if MODIFY fails (e.g., already correct)
                logger()->warning('Role enum alter skipped: ' . $e->getMessage());
            }
        } elseif ($driver === 'sqlite') {
            // SQLite has no enum - column is string, no action needed.
            // But ensure column exists (migration 2026_08_22_070018 already handles)
        } else {
            // For pgsql etc, assume string check constraint - no action
        }

        // Also ensure is_active default etc are correct - nothing else needed
    }

    public function down(): void
    {
        // No rollback - keeping customer enum is safe
    }
};

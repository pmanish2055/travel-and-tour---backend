<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add 'customer' to users.role enum (MySQL only - as requested).
     * Fixes 403 for customer users and ensures AdminSeeder + UserResource form works.
     */
    public function up(): void
    {
        // MySQL only
        try {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('super_admin','admin','editor','agent','customer') NOT NULL DEFAULT 'agent' COMMENT 'Role for Shield - super_admin has all access'");
        } catch (\Exception $e) {
            logger()->warning('Role enum alter skipped: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        // No rollback - keeping customer enum is safe
    }
};

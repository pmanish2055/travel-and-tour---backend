<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * File: database/seeders/DatabaseSeeder.php
 * Purpose: Main seeder entry point. Calls NepalDemoSeeder for demo data and creates default admin if needed.
 *          Run via: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Calls NepalDemoSeeder which seeds regions, packages, etc.
     */
    public function run(): void
    {
        // Call the comprehensive Nepal demo seeder
        $this->call([
            NepalDemoSeeder::class, // Seeds all demo data: regions, destinations, packages, blogs, settings with tokens
        ]);
    }
}

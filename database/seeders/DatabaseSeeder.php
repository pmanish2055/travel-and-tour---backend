<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * File: database/seeders/DatabaseSeeder.php
 * Purpose: Main seeder entry point - ONLY super admin for production (cPanel).
 *          Demo data is NOT seeded by default to keep production clean.
 *          To seed demo data manually: php artisan db:seed --class=NepalDemoSeeder
 *          Run via: php artisan db:seed  OR  php artisan db:seed --class=AdminSeeder
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Only creates: 1) super admin admin@maptechnepal.com / admin123  2) company details (company.name, company.email etc)
     * No demo packages/blogs - as requested "aaru kei cahinna"
     */
    public function run(): void
    {
        // ONLY admin + company details - no demo data (as requested)
        $this->call([
            AdminSeeder::class, // seeds admin + 27 company.* & seo.* keys readable via Setting::get / API
        ]);

        // Optional: Demo data is available but NOT auto-seeded for production
        // Uncomment to seed demo: NepalDemoSeeder::class
        // $this->call([ NepalDemoSeeder::class ]);
    }
}

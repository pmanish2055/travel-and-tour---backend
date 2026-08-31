<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * File: database/seeders/AdminSeeder.php
 * Purpose: Creates ONLY super admin + company details for cPanel production.
 *          No dummy/demo data - only:
 *          1) admin@maptechnepal.com / admin123 (super_admin)
 *          2) company.* settings (company.name, company.email etc - readable via Setting::get / API)
 *          Run: php artisan db:seed --class=AdminSeeder
 *          Or:  php artisan db:seed (via DatabaseSeeder)
 *          Also ensures Shield roles/permissions are generated and super_admin has all permissions.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Super Admin (admin@maptechnepal.com)...');

        // 1. Ensure roles exist
        $roles = ['super_admin', 'admin', 'editor', 'agent', 'panel_user'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 2. Generate permissions if not exists (Shield)
        // Shield uses filament-shield:generate to create permissions from resources/policies
        // We do it via artisan call if permissions table is empty
        if (Permission::count() === 0) {
            try {
                $this->command->info('Generating Shield permissions (php artisan shield:generate --all --panel=admin --option=permissions)...');
                // Need --panel=admin + --option for non-interactive (cPanel) else prompts and hangs (see GenerateCommand.php:83,90)
                \Illuminate\Support\Facades\Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--option' => 'permissions']);
                $this->command->info(\Illuminate\Support\Facades\Artisan::output());
            } catch (\Exception $e) {
                $this->command->warn('Shield generate skipped: ' . $e->getMessage());
                // Fallback: ensure at least basic permissions exist so Gate::before still works
            }
        }

        // 3. Create / update super admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@maptechnepal.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'phone' => '+977-9800000000',
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // If admin already existed but password/role may be outdated, update to required credentials
        $needsUpdate = false;
        $updates = [];
        if ($admin->role !== 'super_admin') {
            $updates['role'] = 'super_admin';
            $needsUpdate = true;
        }
        if (!$admin->is_active) {
            $updates['is_active'] = true;
            $needsUpdate = true;
        }
        if ($admin->name !== 'Super Admin') {
            $updates['name'] = 'Super Admin';
            $needsUpdate = true;
        }
        // Always reset password to admin123 on seed (idempotent)
        // Only if not already correctly hashed? We reset anyway to ensure cPanel can login
        if (!Hash::check('admin123', $admin->password)) {
            $updates['password'] = Hash::make('admin123');
            $needsUpdate = true;
        }
        if ($needsUpdate) {
            $admin->update($updates);
            $this->command->info('Admin updated to admin@maptechnepal.com / admin123');
        }

        // 4. Assign super_admin role (Spatie)
        try {
            if (!$admin->hasRole('super_admin')) {
                $admin->assignRole('super_admin');
                $this->command->info('Assigned super_admin role via Spatie');
            }
            // Ensure panel_user also if needed (Shield panel_user)
            // Not required for super_admin
        } catch (\Exception $e) {
            $this->command->warn('Role assign skipped: ' . $e->getMessage());
        }

        // 5. Give all permissions to super_admin role
        try {
            $superAdminRole = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
            if ($superAdminRole) {
                $permissions = Permission::all();
                if ($permissions->isNotEmpty()) {
                    $superAdminRole->syncPermissions($permissions);
                    $this->command->info('Synced ' . $permissions->count() . ' permissions to super_admin role');
                } else {
                    $this->command->warn('No permissions found to sync - Gate::before will still allow super_admin via AppServiceProvider');
                }
            }
            // Clear permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            $this->command->warn('Permission sync skipped: ' . $e->getMessage());
        }

        $this->command->info('✓ Super Admin ready: admin@maptechnepal.com / admin123');
        $this->command->info('  Login at: /admin');

        // 6. Seed Company Details (company.name, company.email etc - readable via Setting::get and API)
        // This is the "company ko detail" you made in ManageCompanySettings - now seeded for cPanel production
        // Only company + seo groups, NOT demo packages/blogs; aaru kei cahinna as requested
        $this->command->info('Seeding Company Details (company.name, company.email ...)...');
        $companySettings = [
            // Company Identity
            ['key' => 'company.name', 'value' => 'MapTech Nepal Pvt. Ltd.', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Company legal name - shown in footer, invoices, header'],
            ['key' => 'company.tagline', 'value' => 'Discover Nepal with MapTech', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Short tagline for SEO/header'],
            ['key' => 'company.description', 'value' => 'MapTech Nepal is a leading tour and travel operator in Nepal, offering trekking, cultural tours and adventure packages with expert local guides.', 'group' => 'company', 'is_encrypted' => false, 'description' => 'About text for SEO/about page'],
            ['key' => 'company.business_hours', 'value' => '9AM - 6PM, Sunday - Friday', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Footer & contact page'],
            // Contact
            ['key' => 'company.email', 'value' => 'info@maptechnepal.com', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Inquiry notifications go here'],
            ['key' => 'company.phone', 'value' => '+977-1-4440000', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Header, footer tap-to-call'],
            ['key' => 'company.whatsapp', 'value' => '+977-9800000000', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Floating WhatsApp button'],
            ['key' => 'company.address', 'value' => 'Thamel, Kathmandu, Nepal', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Full address - footer & contact map'],
            ['key' => 'company.city', 'value' => 'Kathmandu', 'group' => 'company', 'is_encrypted' => false],
            ['key' => 'company.province', 'value' => 'Bagmati', 'group' => 'company', 'is_encrypted' => false],
            ['key' => 'company.map_embed', 'value' => '', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Google Map embed iframe - leave empty to use lat/lng + API key'],
            // Branding
            ['key' => 'company.logo', 'value' => '', 'group' => 'company', 'is_encrypted' => false, 'description' => '200x60 PNG, header brand logo - upload via Company Settings'],
            ['key' => 'company.favicon', 'value' => '', 'group' => 'company', 'is_encrypted' => false, 'description' => '16x16 or 32x32 favicon'],
            ['key' => 'company.cover', 'value' => '', 'group' => 'company', 'is_encrypted' => false, 'description' => '1200x600 hero cover for About page'],
            ['key' => 'company.primary_color', 'value' => '#f59e0b', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Brand color - amber'],
            // Legal
            ['key' => 'company.pan', 'value' => '123456789', 'group' => 'company', 'is_encrypted' => false, 'description' => 'PAN/VAT number - footer, invoices'],
            ['key' => 'company.reg_no', 'value' => '12345/070/071', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Company registration number'],
            ['key' => 'company.taan_license', 'value' => 'TAAN 1234', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Trekking Agencies Assoc. license'],
            ['key' => 'company.ntb_license', 'value' => 'NTB 5678', 'group' => 'company', 'is_encrypted' => false, 'description' => 'Nepal Tourism Board license'],
            // Social
            ['key' => 'company.facebook', 'value' => '', 'group' => 'company', 'is_encrypted' => false, 'description' => 'https://facebook.com/maptechnepal'],
            ['key' => 'company.instagram', 'value' => '', 'group' => 'company', 'is_encrypted' => false],
            ['key' => 'company.youtube', 'value' => '', 'group' => 'company', 'is_encrypted' => false],
            ['key' => 'company.linkedin', 'value' => '', 'group' => 'company', 'is_encrypted' => false],
            ['key' => 'company.tiktok', 'value' => '', 'group' => 'company', 'is_encrypted' => false],
            // SEO (also readable via API - part of company data)
            ['key' => 'seo.site_title', 'value' => 'MapTech Nepal - Best Trekking & Tour Operator in Nepal', 'group' => 'seo', 'is_encrypted' => false, 'description' => '60 chars Google title'],
            ['key' => 'seo.meta_description', 'value' => 'Book your dream Nepal trek with MapTech Nepal. EBC, Annapurna, Chitwan packages with best price and expert guides.', 'group' => 'seo', 'is_encrypted' => false, 'description' => '160 chars snippet'],
            ['key' => 'seo.keywords', 'value' => 'nepal trek, ebc, annapurna, maptech nepal, tour nepal, trekking', 'group' => 'seo', 'is_encrypted' => false, 'description' => 'Comma keywords for SEO'],
        ];

        $newCount = 0;
        foreach ($companySettings as $s) {
            // Only create if not exists - preserve manual edits via Company Settings page
            // This makes seeder non-destructive: re-running db:seed won't overwrite your changes
            if (!Setting::where('key', $s['key'])->exists()) {
                Setting::set($s['key'], $s['value'], $s['group'], $s['is_encrypted'], $s['description'] ?? null);
                $newCount++;
            }
        }
        $this->command->info('✓ Company details ensured: ' . count($companySettings) . ' keys (company.name, company.email etc), new: ' . $newCount);
        $this->command->info('  Read via: Setting::get(\'company.name\') or GET /api/v1/company or GET /api/v1/settings?group=company');
        $this->command->info('  Edit via: /admin/manage-company-settings (Company Settings page)');
        // Clear settings cache so API reads fresh values immediately
        try {
            foreach (['company', 'seo', 'all'] as $g) {
                \Illuminate\Support\Facades\Cache::forget('settings:' . $g);
            }
        } catch (\Exception $e) {}

        $this->command->info('— Seeder done: ONLY admin + company details (aaru kei cahinna) —');
    }
}

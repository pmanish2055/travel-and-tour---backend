<?php
/**
 * File: config/company.php
 * Purpose: Master config for company details — makes backend reusable for all travel sites.
 *          NOTE: Do NOT query DB (Setting::get) at config load time — causes "connection() on null" error.
 *          This file now returns static env-based defaults only.
 *          Dynamic company settings are fetched at RUNTIME via Setting::get('company.name') in controllers/views,
 *          and via the Filament Company Settings page (Company -> Company Settings at /admin/company-settings).
 *          Change company details there to rebrand without code changes.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Company Master Settings (Static Defaults)
    |--------------------------------------------------------------------------
    | These are fallback defaults used if no DB setting exists yet.
    | After installing, go to Filament: Company -> Company Settings (Master)
    | to override for each travel site. That page saves to `settings` table
    | and is read at runtime via Setting::get(), not at config load.
    |
    */

    // General — use env() with fallback, NOT Setting::get() here. Generic white-label defaults.
    'name' => env('COMPANY_NAME', 'Travel Company'),
    'tagline' => env('COMPANY_TAGLINE', 'Discover Your Next Journey'),
    'description' => env('COMPANY_DESCRIPTION', 'Leading tour operator'),

    // Contact
    'email' => env('COMPANY_EMAIL', 'info@example.com'),
    'phone' => env('COMPANY_PHONE', '+977-1-4000000'),
    'whatsapp' => env('COMPANY_WHATSAPP', '+977-9800000000'),
    'address' => env('COMPANY_ADDRESS', 'Kathmandu, Nepal'),
    'city' => env('COMPANY_CITY', 'Kathmandu'),
    'province' => env('COMPANY_PROVINCE', 'Bagmati'),
    'map_embed' => env('COMPANY_MAP_EMBED', ''),
    'business_hours' => env('COMPANY_BUSINESS_HOURS', '9AM - 6PM, Sunday - Friday'),

    // Branding
    'logo' => env('COMPANY_LOGO', ''),
    'favicon' => env('COMPANY_FAVICON', ''),
    'cover' => env('COMPANY_COVER', ''),
    'primary_color' => env('COMPANY_PRIMARY_COLOR', '#f59e0b'),

    // Legal
    'pan' => env('COMPANY_PAN', '123456789'),
    'reg_no' => env('COMPANY_REG_NO', '12345/070/071'),
    'taan_license' => env('COMPANY_TAAN_LICENSE', 'TAAN 1234'),
    'ntb_license' => env('COMPANY_NTB_LICENSE', 'NTB 5678'),

    // Social
    'facebook' => env('COMPANY_FACEBOOK', ''),
    'instagram' => env('COMPANY_INSTAGRAM', ''),
    'youtube' => env('COMPANY_YOUTUBE', ''),
    'linkedin' => env('COMPANY_LINKEDIN', ''),
    'tiktok' => env('COMPANY_TIKTOK', ''),

    // SEO (master) — generic, overridden per client via Admin → Company Settings
    'seo_site_title' => env('COMPANY_SEO_TITLE', 'Best Trekking & Tour Operator'),
    'seo_meta_description' => env('COMPANY_SEO_DESCRIPTION', 'Book your dream trek & tour packages'),
    'seo_keywords' => env('COMPANY_SEO_KEYWORDS', 'trek, tour, travel, holiday'),

    // Helper: To get dynamic company settings at runtime, use:
    //   \App\Models\Setting::get('company.name')  — reads from DB `settings` table, handles encrypted tokens
    //   See: app/Filament/Pages/ManageCompanySettings.php for master UI
];

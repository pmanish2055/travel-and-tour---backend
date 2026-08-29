<?php
/**
 * File: routes/api.php
 * Purpose: Defines ALL public API routes for frontend consumption (complete version).
 *          All routes prefixed with /api (see bootstrap/app.php) and grouped under /v1.
 *          No auth required for public routes; booking/inquiry/subscribe are open.
 *          Frontend base URL: http://localhost:8000/api/v1  (see frontend/.env VITE_API_URL)
 *          This file completes remaining APIs for full frontend integration: categories, activities, tags, search, coupons, payments, site data.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AddonController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes - Tour & Travel - White-label COMPLETE
|--------------------------------------------------------------------------
| These routes are consumed by the separate frontend (frontend/ folder). Brand is dynamic via Settings (company.name).
| Each controller method has detailed comments explaining purpose.
| Response format: { success: bool, message?: string, data: mixed, meta? } with JSON.
|
| Usage examples (all prefixed with /api):
|   GET  /api/health
|   GET  /api/v1/packages?category=trekking&region=everest&difficulty=easy&search=EBC&tag=adventure&featured=1&per_page=12&sort=price_asc
|   GET  /api/v1/packages/everest-base-camp-trek-14-days
|   GET  /api/v1/search?q=everest&type=packages
|
*/

// Health check - generic white-label
Route::get("/health", function() {
    $name = \App\Models\Setting::get('company.name', config('app.name', 'Travel Platform'));
    return response()->json(["success"=>true, "message"=>$name . " API is running", "version"=>"1.0.0", "time"=>now()->toIso8601String()]);
});

// === v1 group ===
Route::prefix("v1")->group(function() {

    // -------------------------------------------------------------------------
    // Packages: listing, featured, detail, availability, addons
    // -------------------------------------------------------------------------
    Route::get("/packages", [PackageController::class, "index"])->name("api.packages.index"); // GET /api/v1/packages?category=&region=&destination=&difficulty=&tag=&price_min=&price_max=&featured=1&search=&sort=
    Route::get("/packages/featured", [PackageController::class, "featured"])->name("api.packages.featured"); // Homepage featured/trending/popular
    Route::get("/packages/{slug}/addons", [AddonController::class, "forPackage"])->name("api.packages.addons"); // Addons for a package
    // Availability: check seats for a date/pax (optional enhancement - handled via query inside show, but dedicated endpoint)
    Route::get("/packages/{slug}/availability", [PackageController::class, "availability"])->name("api.packages.availability"); // GET /api/v1/packages/{slug}/availability?travel_date=2026-10-01&pax=2
    Route::get("/packages/{slug}", [PackageController::class, "show"])->name("api.packages.show"); // Detail must be last to avoid collision with featured/availability

    // -------------------------------------------------------------------------
    // Regions & Destinations
    // -------------------------------------------------------------------------
    Route::get("/destinations", [DestinationController::class, "index"])->name("api.destinations.index"); // List destinations ?featured=1&region=everest-region
    Route::get("/destinations/featured", [DestinationController::class, "featured"])->name("api.destinations.featured"); // Featured destinations for homepage
    Route::get("/destinations/{slug}", [DestinationController::class, "show"])->name("api.destinations.show"); // Destination detail with packages

    // Regions - new dedicated controller + keep legacy route for BC
    Route::get("/regions", [RegionController::class, "index"])->name("api.regions.index"); // List regions ?with_destinations=1&featured=1
    Route::get("/regions/{slug}", [RegionController::class, "show"])->name("api.regions.show"); // Region detail with destinations + packages
    // Legacy alias kept: DestinationController::regions was old; now points to RegionController
    // Route::get("/regions", [DestinationController::class, "regions"]); // Deprecated - use RegionController::index

    // -------------------------------------------------------------------------
    // Categories (Tour Types)
    // -------------------------------------------------------------------------
    Route::get("/categories", [CategoryController::class, "index"])->name("api.categories.index"); // ?with_packages=1
    Route::get("/categories/{slug}", [CategoryController::class, "show"])->name("api.categories.show"); // ?per_page=12

    // -------------------------------------------------------------------------
    // Activities (Rafting, Safari etc) - M2M with Package
    // -------------------------------------------------------------------------
    Route::get("/activities", [ActivityController::class, "index"])->name("api.activities.index");
    Route::get("/activities/{slug}", [ActivityController::class, "show"])->name("api.activities.show");

    // -------------------------------------------------------------------------
    // Product Tags (SEO tags)
    // -------------------------------------------------------------------------
    Route::get("/tags", [TagController::class, "index"])->name("api.tags.index");
    Route::get("/tags/{slug}", [TagController::class, "show"])->name("api.tags.show");

    // -------------------------------------------------------------------------
    // Addons (Extras for booking)
    // -------------------------------------------------------------------------
    Route::get("/addons", [AddonController::class, "index"])->name("api.addons.index"); // ?package_id=1 or ?package_slug=xxx
    Route::get("/addons/{slug}", [AddonController::class, "show"])->name("api.addons.show");

    // -------------------------------------------------------------------------
    // Blogs + Categories + Tags
    // -------------------------------------------------------------------------
    Route::get("/blogs", [BlogController::class, "index"])->name("api.blogs.index"); // ?category=trekking-tips&search=&featured=1&tag=everest&per_page=9
    Route::get("/blogs/featured", [BlogController::class, "featured"])->name("api.blogs.featured"); // Featured blogs for homepage
    Route::get("/blogs/{slug}", [BlogController::class, "show"])->name("api.blogs.show"); // Detail with related
    Route::get("/blog-categories", [BlogController::class, "categories"])->name("api.blog.categories"); // List blog categories with count
    Route::get("/blog-tags", [BlogController::class, "tags"])->name("api.blog.tags"); // Blog tags list
    // Legacy alias
    // Route::get("/blog-categories", ...) already above

    // -------------------------------------------------------------------------
    // CMS Pages & Static
    // -------------------------------------------------------------------------
    Route::get("/pages", [PageController::class, "index"])->name("api.pages.index"); // List all published pages (for footer sitemap)
    Route::get("/pages/{slug}", [PageController::class, "show"])->name("api.pages.show"); // About, Terms, Privacy, etc

    // -------------------------------------------------------------------------
    // Site-wide aggregates & Homepage
    // -------------------------------------------------------------------------
    Route::get("/homepage", [PageController::class, "homepage"])->name("api.homepage"); // Aggregated homepage (kept for BC)
    Route::get("/site/homepage", [PageController::class, "homepage"])->name("api.site.homepage"); // Alias
    Route::get("/settings", [SiteController::class, "settings"])->name("api.settings"); // ?group=company
    Route::get("/company", [SiteController::class, "company"])->name("api.company"); // Company + SEO settings
    Route::get("/navigation", [SiteController::class, "navigation"])->name("api.navigation"); // Mega menu data
    Route::get("/stats", [SiteController::class, "stats"])->name("api.stats"); // Counts for homepage

    // -------------------------------------------------------------------------
    // Content blocks: sliders, testimonials, team, partners, faqs, why-choose-us
    // -------------------------------------------------------------------------
    Route::get("/sliders", [SiteController::class, "sliders"])->name("api.sliders");
    Route::get("/testimonials", [SiteController::class, "testimonials"])->name("api.testimonials"); // ?featured=1&package_id=1&per_page=12
    Route::get("/team", [SiteController::class, "team"])->name("api.team");
    Route::get("/partners", [SiteController::class, "partners"])->name("api.partners");
    Route::get("/faqs", [SiteController::class, "faqs"])->name("api.faqs"); // ?category=booking
    Route::get("/why-choose-us", [SiteController::class, "whyChooseUs"])->name("api.why");

    // -------------------------------------------------------------------------
    // Search (global) - throttled to prevent abuse/DoS via wildcards
    // -------------------------------------------------------------------------
    Route::middleware('throttle:60,1')->group(function () {
        Route::get("/search", [SearchController::class, "index"])->name("api.search");
        Route::get("/search/suggest", [SearchController::class, "suggest"])->name("api.search.suggest");
    });

    // -------------------------------------------------------------------------
    // Coupons - throttle to prevent brute-force code guessing
    // -------------------------------------------------------------------------
    Route::middleware('throttle:10,1')->group(function () {
        Route::post("/coupons/validate", [CouponController::class, "validate"])->name("api.coupons.validate");
    });
    Route::get("/coupons/{code}", [CouponController::class, "show"])->name("api.coupons.show");

    // -------------------------------------------------------------------------
    // Payments: methods, initiate, verify, history, callbacks - rate limited
    // -------------------------------------------------------------------------
    Route::get("/payments/methods", [PaymentController::class, "methods"])->name("api.payments.methods");
    Route::middleware('throttle:10,1')->group(function () {
        Route::post("/payments/initiate", [PaymentController::class, "initiate"])->name("api.payments.initiate");
        Route::post("/payments/verify", [PaymentController::class, "verify"])->name("api.payments.verify");
    });
    Route::middleware('throttle:30,1')->group(function () {
        Route::get("/payments/booking/{bookingCode}", [PaymentController::class, "bookingPayments"])->name("api.payments.booking");
    });
    Route::get("/payments/callback/esewa/{status}", [PaymentController::class, "esewaCallback"])->name("api.payments.callback.esewa");

    // -------------------------------------------------------------------------
    // Forms: Inquiries & Custom Trips & Bookings & Contact & Newsletter - throttled (anti-spam)
    // -------------------------------------------------------------------------
    Route::middleware('throttle:10,1')->group(function () {
        Route::post("/inquiries", [InquiryController::class, "store"])->name("api.inquiries.store");
        Route::post("/custom-trips", [InquiryController::class, "customTrip"])->name("api.custom-trips.store");
        Route::post("/contact", [ContactController::class, "contact"])->name("api.contact");
        Route::post("/subscribe", [ContactController::class, "subscribe"])->name("api.subscribe");
        Route::post("/testimonials", [SiteController::class, "storeTestimonial"])->name("api.testimonials.store");
    });
    Route::middleware('throttle:20,1')->group(function () {
        Route::post("/bookings", [BookingController::class, "store"])->name("api.bookings.store");
    });
    Route::middleware('throttle:20,1')->group(function () {
        Route::get("/bookings/{code}", [BookingController::class, "show"])->name("api.bookings.show");
        Route::get("/subscribe/check", [ContactController::class, "check"])->name("api.subscribe.check");
    });

});

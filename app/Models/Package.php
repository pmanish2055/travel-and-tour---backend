<?php
/**
 * File: app/Models/Package.php
 * Purpose: CENTRAL MODEL - Represents a tour package (e.g., Everest Base Camp 14 Days).
 *          Contains all trip details, pricing, relations to itinerary, departures, bookings.
 *          Used by: PackageResource (Filament), Api\PackageController, Booking flow, Homepage.
 *          Table: packages
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia; // Spatie media trait for gallery
use Spatie\MediaLibrary\InteractsWithMedia;

class Package extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia; // InteractsWithMedia: handles media library uploads

    protected $fillable = [
        'category_id', 'destination_id', 'region_id', // Relations
        'title', 'slug', 'short_description', 'overview', 'highlights', // Content
        'duration_days', 'duration_nights', 'group_size_min', 'group_size_max', 'max_altitude_m', 'difficulty', 'best_season', 'accommodation', 'meal_plan', 'transportation', 'trip_type', // Trip specifics
        'price', 'discount_price', 'price_type', 'currency', 'is_price_on_request', // Pricing
        'featured', 'is_trending', 'is_popular', 'status', 'published_at', // Visibility
        'featured_image', 'gallery', 'video_url', 'map_embed', 'seo_title', 'seo_description', 'seo_keywords', 'view_count', 'sort_order'
    ];

    protected $casts = [
        'highlights' => 'array', // JSON -> array
        'best_season' => 'array',
        'gallery' => 'array',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_popular' => 'boolean',
        'is_price_on_request' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    protected static function booted(): void
    {
        // Auto-generate slug on create
        static::creating(function ($p) {
            if (empty($p->slug)) $p->slug = Str::slug($p->title);
            if (empty($p->published_at) && $p->status === 'published') $p->published_at = now();
        });
        static::updating(function ($p) {
            if ($p->isDirty('title') && empty($p->slug)) $p->slug = Str::slug($p->title);
        });
    }

    // === Media Library Collections ===
    /**
     * Register media collections for Package.
     * Used by: Filament FileUpload with collection names, frontend gallery.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery')->comment('Gallery images for package detail slider');
        $this->addMediaCollection('featured')->singleFile()->comment('Single featured image');
    }

    // === Relationships ===
    /** Category this package belongs to */
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    /** Destination */
    public function destination(): BelongsTo { return $this->belongsTo(Destination::class); }
    /** Region */
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }
    /** Day-wise itineraries */
    public function itineraries(): HasMany { return $this->hasMany(PackageItinerary::class)->orderBy('day_number'); }
    /** Inclusions (includes + excludes via type) */
    public function inclusions(): HasMany { return $this->hasMany(PackageInclusion::class); }
    /** Helper: only includes */
    public function includes(): HasMany { return $this->hasMany(PackageInclusion::class)->where('type','include'); }
    /** Helper: only excludes */
    public function excludes(): HasMany { return $this->hasMany(PackageInclusion::class)->where('type','exclude'); }
    /** FAQs */
    public function faqs(): HasMany { return $this->hasMany(PackageFaq::class); }
    /** Equipment */
    public function equipment(): HasMany { return $this->hasMany(PackageEquipment::class); }
    /** Departures */
    public function departures(): HasMany { return $this->hasMany(PackageDeparture::class); }
    /** Bookings */
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    /** Testimonials */
    public function testimonials(): HasMany { return $this->hasMany(Testimonial::class); }
    /** Activities (M2M) */
    public function activities(): BelongsToMany { return $this->belongsToMany(Activity::class, 'activity_package'); }
    /** Addons (M2M) */
    public function addons(): BelongsToMany { return $this->belongsToMany(Addon::class, 'addon_package'); }
    /** Tags for SEO (M2M) — product tags like Family, Adventure, Luxury, used in SEO tab */
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class, 'package_tag'); }
    /** Pricing tiers — handles Single vs Group pricing per your request (shown in Pricing tab) */
    public function pricings(): HasMany { return $this->hasMany(PackagePricing::class)->orderBy('sort_order'); }
    /** Helper: single pricing tiers */
    public function singlePricings(): HasMany { return $this->hasMany(PackagePricing::class)->where('type','single'); }
    /** Helper: group pricing tiers */
    public function groupPricings(): HasMany { return $this->hasMany(PackagePricing::class)->where('type','group'); }
    /** Inquiries */
    public function inquiries(): HasMany { return $this->hasMany(Inquiry::class); }

    // === Scopes ===
    public function scopePublished($q) { return $q->where('status','published'); }
    public function scopeFeatured($q) { return $q->where('featured', true); }
    public function scopePopular($q) { return $q->where('is_popular', true); }

    // === Helpers ===
    /**
     * Get final display price (discount if exists else base).
     * Used in: Frontend cards, booking total calc.
     * @return float
     */
    public function finalPrice(): float { return $this->discount_price ?? $this->price; }

    /**
     * Check if package is on sale.
     * @return bool
     */
    public function isOnSale(): bool { return !is_null($this->discount_price) && $this->discount_price < $this->price; }

    /**
     * Get discount percentage.
     * @return int
     */
    public function discountPercentage(): int
    {
        if (!$this->isOnSale()) return 0;
        return (int) round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * Increment view count.
     */
    public function incrementViews(): void { $this->increment('view_count'); }
}

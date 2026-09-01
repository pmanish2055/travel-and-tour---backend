<?php
/**
 * File: app/Models/Destination.php
 * Purpose: Represents a specific destination (Poon Hill, EBC, Phewa Lake) within a Region.
 *          Used to filter packages by destination. Shown on /destinations/{slug}.
 *          Table: destinations
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'region_id', // FK to regions
        'name', // Destination name
        'slug', // URL slug
        'overview', // Long overview
        'short_description', // Short excerpt
        'altitude_m', // Altitude
        'latitude', // GPS lat
        'longitude', // GPS lng
        'featured_image', // Image
        'best_season', // JSON seasons
        'is_featured', // Featured
        'is_active', // Active
        'seo_title', // SEO
        'seo_description',
        'sort_order',
        'video_url',
        'gallery',
        'map_embed',
    ];

    protected $casts = [
        'best_season' => 'array', // Auto decode JSON to array
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function ($d) { if (empty($d->slug)) $d->slug = Str::slug($d->name); });
    }

    /** Region this destination belongs to */
    public function region(): BelongsTo { return $this->belongsTo(Region::class); }

    /** Packages for this destination */
    public function packages(): HasMany { return $this->hasMany(Package::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
}

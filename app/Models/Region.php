<?php
/**
 * File: app/Models/Region.php
 * Purpose: Represents a Nepal travel region (Everest, Annapurna, etc). Hierarchical via parent_id.
 *          Used by: Destination (belongs to region), Package (via region_id), Filament RegionResource.
 *          Table: regions
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Region
 * Hierarchical region for grouping destinations.
 */
class Region extends Model
{
    use HasFactory, SoftDeletes; // SoftDeletes: allows soft delete (deleted_at column)

    /**
     * Mass assignable attributes.
     * @var array
     */
    protected $fillable = [
        'parent_id', // Parent region ID for hierarchy
        'name', // Region name
        'slug', // URL slug
        'description', // Description
        'featured_image', // Image path
        'is_featured', // Featured flag
        'is_active', // Active flag
        'sort_order', // Sort order
        'seo_title', // SEO title
        'seo_description', // SEO description
    ];

    /**
     * Attribute casts - auto type conversion.
     * @var array
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // ==================== BOOT & SLUG ====================
    /**
     * Boot method: Auto-generate slug from name on creating/updating.
     * Called automatically by Eloquent when model events fire.
     */
    protected static function booted(): void
    {
        static::creating(function ($region) {
            if (empty($region->slug)) {
                $region->slug = Str::slug($region->name); // Generate slug from name
            }
        });
        static::updating(function ($region) {
            if ($region->isDirty('name') && empty($region->slug)) {
                $region->slug = Str::slug($region->name);
            }
        });
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Parent region (self-referential).
     * E.g., Khumbu parent is Everest Region.
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'parent_id'); // Self join via parent_id
    }

    /**
     * Child regions.
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Region::class, 'parent_id'); // Inverse of parent
    }

    /**
     * Destinations in this region.
     * @return HasMany
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class); // region_id FK in destinations
    }

    /**
     * Packages directly linked to region (denormalized for fast filter).
     * @return HasMany
     */
    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    // ==================== SCOPES ====================
    /**
     * Scope active regions only.
     */
    public function scopeActive($query) { return $query->where('is_active', true); }

    /**
     * Scope featured regions.
     */
    public function scopeFeatured($query) { return $query->where('is_featured', true); }

    /**
     * Scope top-level (no parent).
     */
    public function scopeRoot($query) { return $query->whereNull('parent_id'); }
}

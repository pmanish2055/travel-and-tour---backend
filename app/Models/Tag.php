<?php
/**
 * File: app/Models/Tag.php
 * Purpose: Product tag for SEO and filtering (e.g., Family, Adventure, Budget, Luxury, EBC, Annapurna).
 *          Many-to-many with Package via package_tag pivot. Used in Package SEO tab.
 *          Table: tags + pivot package_tag
 *          Filament: TagResource (hidden, managed via Package SEO tab) or standalone if needed.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    // === Mass assignable ===
    protected $fillable = [
        'name', // Tag name e.g., Adventure
        'slug', // Slug for filtering
        'color', // Badge color
        'description', // SEO description
        'is_active', // Active flag
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // === Auto-generate slug on create ===
    protected static function booted(): void
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    // === Relationship: Packages that have this tag ===
    /**
     * Packages with this tag (M2M)
     * @return BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_tag');
    }

    // === Scope: active only ===
    public function scopeActive($q) { return $q->where('is_active', true); }
}

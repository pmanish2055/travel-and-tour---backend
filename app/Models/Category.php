<?php
/**
 * File: app/Models/Category.php
 * Purpose: Tour category (Trekking, Cultural Tour, etc). Packages belong to category.
 *          Used for filtering, navigation, Filament CategoryResource.
 *          Table: categories
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 'featured_image',
        'is_active', 'sort_order', 'seo_title', 'seo_description'
    ];
    protected $casts = ['is_active' => 'boolean'];
    protected static function booted(): void
    {
        static::creating(function ($c) { if (empty($c->slug)) $c->slug = Str::slug($c->name); });
    }
    /** Packages in this category */
    public function packages(): HasMany { return $this->hasMany(Package::class); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}

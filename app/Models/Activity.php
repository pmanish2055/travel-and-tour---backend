<?php
/**
 * File: app/Models/Activity.php
 * Purpose: Activity entity (Rafting, Safari, etc) - many-to-many with Package.
 *          Selected as tags in package form.
 *          Table: activities
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','icon','description','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    protected static function booted(): void
    {
        static::creating(function ($a) { if (empty($a->slug)) $a->slug = Str::slug($a->name); });
    }
    /** Packages that have this activity */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'activity_package'); // Pivot table
    }
}

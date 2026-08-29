<?php
/**
 * File: app/Models/Addon.php
 * Purpose: Extra purchasable service (Porter, Extra Night, etc). M2M with Package.
 *          Table: addons
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Addon extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','description','price','price_type','icon','is_active','sort_order'];
    protected $casts = ['price'=>'decimal:2','is_active'=>'boolean'];
    protected static function booted(): void { static::creating(function ($a){ if(empty($a->slug)) $a->slug = Str::slug($a->name); }); }
    public function packages(): BelongsToMany { return $this->belongsToMany(Package::class, 'addon_package'); }
}

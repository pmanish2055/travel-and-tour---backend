<?php
/**
 * File: app/Models/PackagePricing.php
 * Purpose: Pricing tier for a package — handles Single vs Group pricing as you requested.
 *          Each package can have multiple tiers: Single traveler, Group 2-4, Group 5-8, etc.
 *          Shown in Package "Pricing" tab as Repeater.
 *          Table: package_pricings
 *          Belongs to Package.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagePricing extends Model
{
    use HasFactory;

    // === Mass assignable ===
    protected $fillable = [
        'package_id', // FK to packages
        'title', // e.g., Single Traveler, Group 2-4 Pax
        'type', // single, group, private, fixed
        'pax_min', // Min pax
        'pax_max', // Max pax (null = unlimited)
        'price_per_person', // Price per person
        'total_price', // Optional total group price
        'currency', // NPR/USD
        'description', // Description
        'is_active', // Active flag
        'sort_order', // Ordering
    ];

    protected $casts = [
        'pax_min' => 'integer',
        'pax_max' => 'integer',
        'price_per_person' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // === Relationship: Parent package ===
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // === Helper: Display label for pricing tier ===
    public function displayLabel(): string
    {
        return $this->title . ' (' . $this->pax_min . ($this->pax_max ? '-'.$this->pax_max : '+') . ' pax) : $' . $this->price_per_person . '/pp';
    }

    // === Scope: active only ===
    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort_order'); }
}

<?php
/**
 * File: app/Models/Coupon.php
 * Purpose: Discount coupon code (fixed or percent) for packages.
 *          Table: coupons + pivot coupon_package
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;
    protected $fillable = ['code','discount_type','value','valid_from','valid_to','usage_limit','used_count','is_active'];
    protected $casts = ['value'=>'decimal:2','valid_from'=>'date','valid_to'=>'date','is_active'=>'boolean'];
    public function packages(): BelongsToMany { return $this->belongsToMany(Package::class, 'coupon_package'); }
    public function isValid(): bool {
        if(!$this->is_active) return false;
        if($this->valid_from && now()->lt($this->valid_from)) return false;
        if($this->valid_to && now()->gt($this->valid_to)) return false;
        if($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }
    public function scopeActive($q){ return $q->where('is_active', true); }
}

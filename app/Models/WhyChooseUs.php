<?php
/**
 * File: app/Models/WhyChooseUs.php
 * Purpose: Why Choose Us feature item (Expert Guides, Best Price, etc) for homepage.
 *          Table: why_choose_us
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WhyChooseUs extends Model
{
    use HasFactory;
    protected $table = 'why_choose_us'; // Explicit table
    protected $fillable = ['title','description','icon','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function scopeActive($q){ return $q->where('is_active', true)->orderBy('sort_order'); }
}

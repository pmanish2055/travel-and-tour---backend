<?php
/**
 * File: app/Models/Faq.php
 * Purpose: Global FAQ (not package-specific) for footer/FAQ page.
 *          Table: faqs
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faq extends Model
{
    use HasFactory;
    protected $fillable = ['question','answer','category','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function scopeActive($q){ return $q->where('is_active', true)->orderBy('sort_order'); }
}

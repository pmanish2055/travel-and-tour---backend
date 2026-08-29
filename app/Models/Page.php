<?php
/**
 * File: app/Models/Page.php
 * Purpose: Static CMS page (About Us, Terms, Privacy) for /pages/{slug}.
 *          Table: pages
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['title','slug','content','template','seo_title','seo_description','is_system','status'];
    protected $casts = ['is_system'=>'boolean'];
    protected static function booted(): void { static::creating(function($p){ if(empty($p->slug)) $p->slug = Str::slug($p->title); }); }
    public function scopePublished($q){ return $q->where('status','published'); }
}

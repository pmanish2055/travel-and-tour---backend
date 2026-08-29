<?php
/**
 * File: app/Models/BlogCategory.php
 * Purpose: Blog category (Travel Tips, News, etc). Has many posts.
 *          Table: blog_categories
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','description','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    protected static function booted(): void { static::creating(function($c){ if(empty($c->slug)) $c->slug = Str::slug($c->name); }); }
    public function posts(): HasMany { return $this->hasMany(BlogPost::class); }
}

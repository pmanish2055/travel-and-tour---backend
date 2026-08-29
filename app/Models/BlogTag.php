<?php
/**
 * File: app/Models/BlogTag.php
 * Purpose: Tag for blog posts (many-to-many).
 *          Table: blog_tags
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug'];
    protected static function booted(): void { static::creating(function($t){ if(empty($t->slug)) $t->slug = Str::slug($t->name); }); }
    public function posts(): BelongsToMany { return $this->belongsToMany(BlogPost::class, 'blog_post_tag'); }
}

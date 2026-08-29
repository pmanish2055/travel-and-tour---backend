<?php
/**
 * File: app/Models/BlogPost.php
 * Purpose: Blog post/article for SEO/content marketing. Belongs to category & author (user).
 *          Has many tags (M2M). Used in BlogPostResource and Api\BlogController.
 *          Table: blog_posts
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BlogPost extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    protected $fillable = [
        'title','slug','blog_category_id','user_id','excerpt','content','featured_image',
        'is_featured','status','published_at','view_count','seo_title','seo_description'
    ];
    protected $casts = ['is_featured'=>'boolean','published_at'=>'datetime','view_count'=>'integer'];
    protected static function booted(): void {
        static::creating(function($p){
            if(empty($p->slug)) $p->slug = Str::slug($p->title);
            if(empty($p->published_at) && $p->status==='published') $p->published_at = now();
        });
    }
    public function registerMediaCollections(): void {
        $this->addMediaCollection('gallery'); // For blog gallery
        $this->addMediaCollection('featured')->singleFile();
    }
    public function category(): BelongsTo { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(BlogTag::class, 'blog_post_tag'); }
    public function scopePublished($q){ return $q->where('status','published'); }
    public function scopeFeatured($q){ return $q->where('is_featured', true); }
    public function incrementViews(): void { $this->increment('view_count'); }
}

<?php
/**
 * File: app/Http/Controllers/Api/BlogController.php
 * Purpose: Public blog API for frontend.
 *          Routes: GET /api/v1/blogs, GET /api/v1/blogs/{slug}, GET /api/v1/blog-categories
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /** List published posts with pagination and category filter */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);
        $query = BlogPost::with(["category","author","tags"])->published()->latest("published_at");
        if ($request->filled("category")) {
            $query->whereHas("category", fn($q)=>$q->where("slug",$request->category));
        }
        if ($request->filled("search")) {
            $like = '%'.addcslashes($request->search, '%_\\').'%';
            $query->where("title","like",$like);
        }
        $perPage = min((int) $request->input("per_page",9), 50);
        $posts = $query->paginate($perPage);
        return response()->json(["success"=>true, "data"=>$posts]);
    }

    /** Show single post by slug, increment view count (throttled 1/hour per IP) */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::with(["category","author","tags","media"])->where("slug",$slug)->published()->first();
        if (!$post) return response()->json(["success"=>false,"message"=>"Post not found"],404);
        $key = 'view:blog:'.$post->id.':'.request()->ip();
        if (Cache::add($key, 1, 3600)) {
            $post->incrementViews();
        }
        $related = BlogPost::published()->where("id","!=",$post->id)->where("blog_category_id",$post->blog_category_id)->limit(3)->get();
        return response()->json(["success"=>true, "data"=>["post"=>$post, "related"=>$related]]);
    }

    /** List categories */
    public function categories(): JsonResponse
    {
        $cats = BlogCategory::withCount("posts")->get();
        return response()->json(["success"=>true, "data"=>$cats]);
    }

    /** List tags */
    public function tags(): JsonResponse
    {
        $tags = \App\Models\BlogTag::withCount("posts")->get();
        return response()->json(["success"=>true, "data"=>$tags]);
    }

    /** Featured blogs for homepage */
    public function featured(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 6), 20);
        $posts = BlogPost::with(["category","author","tags"])
            ->published()
            ->where('is_featured', true)
            ->latest("published_at")
            ->limit($limit)
            ->get();
        return response()->json(["success"=>true, "data"=>$posts]);
    }
}

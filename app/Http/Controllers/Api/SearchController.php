<?php
/**
 * File: app/Http/Controllers/Api/SearchController.php
 * Purpose: Global search across packages, destinations, regions, blogs for frontend search bar & autocomplete.
 *          Supports ?q=keyword and ?type=packages|destinations|blogs|all
 *          Routes: GET /api/v1/search, GET /api/v1/search/suggest
 *          Frontend: Header search input, Packages search, Global Search page
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Destination;
use App\Models\Region;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search endpoint.
     * Query params:
     * - q: search keyword (required, min 2 chars)
     * - type: all|packages|destinations|blogs (default all)
     * - per_page: pagination for primary type
     * @return JsonResponse unified search results
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|in:all,packages,destinations,regions,blogs',
            'per_page' => 'nullable|integer|min:1|max:20'
        ]);

        $q = $request->input('q');
        $type = $request->input('type', 'all');
        $perPage = (int) $request->input('per_page', 10);
        $like = '%'.addcslashes($q, '%_\\').'%';

        $results = [];

        // Packages search: title, short_description, overview
        if (in_array($type, ['all', 'packages'])) {
            $packages = Package::with(['category', 'destination'])
                ->published()
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                          ->orWhere('short_description', 'like', $like)
                          ->orWhere('overview', 'like', $like);
                })
                ->orderBy('sort_order')
                ->limit($type === 'all' ? 5 : $perPage)
                ->get(['id', 'title', 'slug', 'short_description', 'price', 'discount_price', 'duration_days', 'difficulty', 'featured_image', 'category_id', 'destination_id']);
            $results['packages'] = $packages;
            $results['packages_count'] = $packages->count();
        }

        // Destinations search
        if (in_array($type, ['all', 'destinations', 'regions'])) {
            $destinations = Destination::with('region')
                ->where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('short_description', 'like', $like)
                          ->orWhere('overview', 'like', $like);
                })
                ->limit($type === 'all' ? 5 : $perPage)
                ->get(['id', 'name', 'slug', 'short_description', 'region_id', 'featured_image']);
            $results['destinations'] = $destinations;

            $regions = Region::where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                          ->orWhere('description', 'like', $like);
                })
                ->limit(5)
                ->get(['id', 'name', 'slug', 'description']);
            $results['regions'] = $regions;
        }

        // Blogs search
        if (in_array($type, ['all', 'blogs'])) {
            $blogs = BlogPost::with(['category'])
                ->published()
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                          ->orWhere('excerpt', 'like', $like)
                          ->orWhere('content', 'like', $like);
                })
                ->limit($type === 'all' ? 5 : $perPage)
                ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'blog_category_id', 'published_at']);
            $results['blogs'] = $blogs;
        }

        // Total count for "all" mode
        $total = 0;
        foreach (['packages', 'destinations', 'blogs'] as $k) {
            if (isset($results[$k])) $total += $results[$k]->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'Search results for: ' . $q,
            'query' => $q,
            'type' => $type,
            'total' => $total,
            'data' => $results
        ]);
    }

    /**
     * Suggest / autocomplete for search bar.
     * Returns lightweight titles + slugs for quick dropdown.
     * GET /api/v1/search/suggest?q=everest&limit=5
     */
    public function suggest(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
            'limit' => 'nullable|integer|min:1|max:10'
        ]);

        $q = $request->input('q');
        $limit = (int) $request->input('limit', 5);
        $like = '%'.addcslashes($q, '%_\\').'%';

        $packages = Package::published()
            ->where('title', 'like', $like)
            ->limit($limit)
            ->get(['title', 'slug', 'price', 'duration_days']);

        $destinations = Destination::where('is_active', true)
            ->where('name', 'like', $like)
            ->limit(3)
            ->get(['name', 'slug']);

        $blogs = BlogPost::published()
            ->where('title', 'like', $like)
            ->limit(3)
            ->get(['title', 'slug']);

        return response()->json([
            'success' => true,
            'data' => [
                'packages' => $packages,
                'destinations' => $destinations,
                'blogs' => $blogs,
            ]
        ]);
    }
}

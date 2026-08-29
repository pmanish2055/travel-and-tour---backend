<?php
/**
 * File: app/Http/Controllers/Api/TagController.php
 * Purpose: Product Tags for SEO (Family, Adventure, Budget, Luxury, EBC, etc) - M2M with Package.
 *          Tags are used for SEO filtering and frontend badges.
 *          Routes: GET /api/v1/tags, GET /api/v1/tags/{slug}
 *          Model: App\Models\Tag
 *          Pivot: package_tag
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * List active tags with packages count.
     * Useful for package filter chips and SEO cloud.
     */
    public function index(Request $request): JsonResponse
    {
        $tags = Tag::where('is_active', true)
            ->withCount(['packages' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Product tags fetched successfully',
            'data' => $tags
        ]);
    }

    /**
     * Show tag detail by slug with packages that have this tag.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)->where('is_active', true)->first();
        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'Tag not found'], 404);
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        $packages = $tag->packages()
            ->with(['category', 'destination', 'region'])
            ->published()
            ->orderBy('sort_order')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'tag' => $tag->loadCount(['packages' => fn($q) => $q->published()]),
                'packages' => $packages
            ]
        ]);
    }
}

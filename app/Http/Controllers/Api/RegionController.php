<?php
/**
 * File: app/Http/Controllers/Api/RegionController.php
 * Purpose: Regions (Everest, Annapurna, Langtang, etc) hierarchical controller.
 *          Previously DestinationController only had regions() list - this adds full show with destinations + packages.
 *          Routes: GET /api/v1/regions, GET /api/v1/regions/{slug}
 *          Model: App\Models\Region
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RegionController extends Controller
{
    /**
     * List all active regions with optional hierarchy.
     * ?with_destinations=1 includes destinations count + preview.
     * ?parent_id=1 filter by parent
     */
    public function index(Request $request): JsonResponse
    {
        $query = Region::active()->orderBy('sort_order');

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $query->withCount(['destinations', 'packages' => fn($q) => $q->published()]);

        if ($request->boolean('with_destinations')) {
            $query->with(['destinations' => fn($q) => $q->active()->orderBy('sort_order')->limit(6), 'children']);
        }

        $cacheKey = 'regions:index:'.md5(json_encode($request->only(['parent_id','featured','with_destinations'])));
        $regions = Cache::remember($cacheKey, 3600, fn() => $query->get());

        return response()->json([
            'success' => true,
            'message' => 'Regions fetched successfully',
            'data' => $regions
        ]);
    }

    /**
     * Show single region by slug with destinations and packages.
     * Also returns child regions if hierarchical.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $region = Region::with([
            'destinations' => fn($q) => $q->active()->orderBy('sort_order'),
            'children',
            'parent'
        ])->withCount(['packages' => fn($q) => $q->published()])
          ->where('slug', $slug)
          ->first();

        if (!$region) {
            return response()->json(['success' => false, 'message' => 'Region not found'], 404);
        }

        // Paginated packages for this region
        $perPage = min((int) $request->input('per_page', 12), 50);
        $packages = $region->packages()
            ->with(['category', 'destination'])
            ->published()
            ->orderBy('sort_order')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'region' => $region,
                'packages' => $packages,
            ]
        ]);
    }
}

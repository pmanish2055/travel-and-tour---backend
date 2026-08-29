<?php
/**
 * File: app/Http/Controllers/Api/DestinationController.php
 * Purpose: API for destinations and regions.
 *          Routes: GET /api/v1/destinations, GET /api/v1/destinations/{slug}, GET /api/v1/regions
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DestinationController extends Controller
{
    /**
     * List all active destinations with region.
     */
    public function index(): JsonResponse
    {
        $destinations = Cache::remember('destinations:active', 3600, fn() => Destination::with("region")->active()->orderBy("sort_order")->get());
        return response()->json(["success"=>true, "data"=>$destinations]);
    }

    /**
     * Show destination by slug with packages count.
     */
    public function show(string $slug): JsonResponse
    {
        $destination = Destination::with(["region", "packages" => fn($q)=>$q->published()->limit(6)])->where("slug",$slug)->first();
        if (!$destination) return response()->json(["success"=>false,"message"=>"Destination not found"],404);
        return response()->json(["success"=>true, "data"=>$destination]);
    }

    /**
     * List regions with destinations count.
     * Kept for backward compatibility - new RegionController::index is preferred.
     */
    public function regions(): JsonResponse
    {
        $regions = Region::withCount("destinations")->active()->orderBy("sort_order")->get();
        return response()->json(["success"=>true, "data"=>$regions]);
    }

    /**
     * Featured destinations for homepage grid.
     */
    public function featured(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = min((int) $request->input('limit', 6), 20);
        $destinations = Destination::with(['region'])
            ->active()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->limit($limit)
            ->get();

        return response()->json(['success' => true, 'data' => $destinations]);
    }
}

<?php
/**
 * File: app/Http/Controllers/Api/AddonController.php
 * Purpose: Addons (Extra services) that can be added to a package booking.
 *          e.g., Porter, Extra Night, Helicopter, etc. M2M with Package.
 *          Routes: GET /api/v1/addons, GET /api/v1/addons/{slug}, GET /api/v1/packages/{slug}/addons
 *          Model: App\Models\Addon
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * List active addons.
     * Can filter by ?package_id=1 or ?package_slug=everest-trek to get only addons for that package.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Addon::where('is_active', true)->orderBy('sort_order');

        if ($request->filled('package_id')) {
            $query->whereHas('packages', fn($q) => $q->where('packages.id', $request->package_id));
        }
        if ($request->filled('package_slug')) {
            $query->whereHas('packages', fn($q) => $q->where('packages.slug', $request->package_slug));
        }

        $addons = $query->get();

        return response()->json([
            'success' => true,
            'data' => $addons
        ]);
    }

    /**
     * Show single addon by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $addon = Addon::where('slug', $slug)->where('is_active', true)->first();
        if (!$addon) {
            return response()->json(['success' => false, 'message' => 'Addon not found'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $addon->load(['packages' => fn($q) => $q->published()->limit(5)])
        ]);
    }

    /**
     * Get addons for a specific package by slug.
     * GET /api/v1/packages/{slug}/addons
     */
    public function forPackage(string $slug): JsonResponse
    {
        $package = Package::where('slug', $slug)->published()->first();
        if (!$package) {
            return response()->json(['success' => false, 'message' => 'Package not found'], 404);
        }
        $addons = $package->addons()->where('is_active', true)->orderBy('sort_order')->get();
        return response()->json([
            'success' => true,
            'data' => [
                'package' => ['id' => $package->id, 'slug' => $package->slug, 'title' => $package->title],
                'addons' => $addons
            ]
        ]);
    }
}

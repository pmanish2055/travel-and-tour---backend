<?php
/**
 * File: app/Http/Controllers/Api/ActivityController.php
 * Purpose: API for Activities (Rafting, Paragliding, Jungle Safari, etc) - M2M with Package.
 *          Frontend uses this for filtering packages by activity and displaying activity badges.
 *          Routes: GET /api/v1/activities, GET /api/v1/activities/{slug}
 *          Model: App\Models\Activity
 *          Filament: ActivityResource at /admin/activities
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * List active activities with package counts.
     * Used for homepage activity grid and filter dropdown.
     */
    public function index(Request $request): JsonResponse
    {
        $activities = Activity::where('is_active', true)
            ->withCount(['packages' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Activities fetched successfully',
            'data' => $activities
        ]);
    }

    /**
     * Show activity detail by slug with packages.
     * @param string $slug e.g., rafting
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $activity = Activity::where('slug', $slug)->where('is_active', true)->first();

        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Activity not found'], 404);
        }

        $perPage = min((int) $request->input('per_page', 12), 50);
        $packages = $activity->packages()
            ->with(['category', 'destination', 'region'])
            ->published()
            ->orderBy('sort_order')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'activity' => $activity->loadCount(['packages' => fn($q) => $q->published()]),
                'packages' => $packages
            ]
        ]);
    }
}

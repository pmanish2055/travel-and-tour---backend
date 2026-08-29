<?php
/**
 * File: app/Http/Controllers/Api/CategoryController.php
 * Purpose: API endpoints for frontend to fetch tour categories (Trekking, Cultural Tour, Peak Climbing, etc)
 *          Provides active categories list and single category detail with packages.
 *          Routes: GET /api/v1/categories, GET /api/v1/categories/{slug}
 *          Used by: frontend Filters dropdown, Category listing page, Navigation
 *          Related Model: App\Models\Category
 *          Filament: CategoryResource at /admin/categories
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * List all active categories ordered by sort_order.
     * Each category includes packages count for display badges.
     * Query params: ?featured=1, ?with_packages=1
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::active()->orderBy('sort_order')->orderBy('name');

        // Optional: only featured categories (if you add is_featured later)
        // if ($request->boolean('featured')) { $query->where('is_featured', true); }

        // Include packages count for frontend badges
        $query->withCount(['packages' => fn($q) => $q->published()]);

        // Optional: eager load 3 packages per category for homepage sections
        if ($request->boolean('with_packages')) {
            $query->with(['packages' => fn($q) => $q->published()->limit(3)->with(['destination', 'region'])]);
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories,
        ]);
    }

    /**
     * Show single category by slug with packages list.
     * Used for category detail page: /categories/{slug}
     * Paginates packages inside category.
     * @param string $slug Category slug e.g., trekking
     * @return JsonResponse
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $category = Category::withCount(['packages' => fn($q) => $q->published()])
            ->where('slug', $slug)
            ->first();

        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        // Fetch packages for this category with pagination
        $perPage = min((int) $request->input('per_page', 12), 50);
        $packages = $category->packages()
            ->with(['destination', 'region', 'category', 'tags', 'pricings' => fn($q) => $q->where('is_active', true)])
            ->published()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'packages' => $packages,
            ]
        ]);
    }
}

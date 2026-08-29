<?php
/**
 * File: app/Http/Controllers/Api/PackageController.php
 * Purpose: API endpoints for frontend to fetch packages.
 *          Used by: frontend /packages listing, /packages/{slug} detail, homepage featured.
 *          Routes: GET /api/v1/packages, GET /api/v1/packages/{slug}
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Class PackageController
 * Handles public package listing and detail for frontend consumption.
 */
class PackageController extends Controller
{
    /**
     * List packages with filtering, search, pagination.
     * Supports query params: category, region, destination, difficulty, price_min, price_max, featured, search, sort, per_page
     * @param Request $request
     * @return JsonResponse Paginated packages with relations
     */
    public function index(Request $request): JsonResponse
    {
        // Start query: only published packages for public API — now includes tags + pricings for SEO and Single/Group pricing
        $query = Package::with(["category", "destination", "region", "activities", "tags", "pricings" => fn($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->published() // Scope: status = published
            ->orderBy("sort_order") // Respect manual order
            ->orderBy("created_at", "desc");

        // === Filters ===
        // Filter by category slug (e.g., ?category=trekking)
        if ($request->filled("category")) {
            $query->whereHas("category", fn($q) => $q->where("slug", $request->category));
        }
        // Filter by region slug
        if ($request->filled("region")) {
            $query->whereHas("region", fn($q) => $q->where("slug", $request->region));
        }
        // Filter by destination slug
        if ($request->filled("destination")) {
            $query->whereHas("destination", fn($q) => $q->where("slug", $request->destination));
        }
        // Filter by difficulty
        if ($request->filled("difficulty")) {
            $query->where("difficulty", $request->difficulty);
        }
        // Price range
        if ($request->filled("price_min")) {
            $query->where("price", ">=", $request->price_min);
        }
        if ($request->filled("price_max")) {
            $query->where("price", "<=", $request->price_max);
        }
        // Featured only
        if ($request->boolean("featured")) {
            $query->where("featured", true);
        }
        // Filter by product tag (SEO) — as you requested product tags
        if ($request->filled("tag")) {
            $query->whereHas("tags", fn($q) => $q->where("slug", $request->tag));
        }
        // Search in title, overview - escape wildcards to prevent DoS
        if ($request->filled("search")) {
            $search = $request->search;
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function($q) use ($like) {
                $q->where("title", "like", $like)
                  ->orWhere("short_description", "like", $like)
                  ->orWhere("overview", "like", $like);
            });
        }
        // Sorting: price, duration, title
        if ($request->filled("sort")) {
            match($request->sort) {
                "price_asc" => $query->orderBy("price", "asc"),
                "price_desc" => $query->orderBy("price", "desc"),
                "duration_asc" => $query->orderBy("duration_days", "asc"),
                default => null
            };
        }

        $perPage = min((int) $request->input("per_page", 12), 50);
        $packages = $query->paginate($perPage);
        // Transform via Resource but keep Laravel pagination envelope inside `data` for frontend compat (res.data.data.data)
        $packages->getCollection()->transform(fn($p) => (new PackageResource($p))->toArray($request));

        return response()->json([
            "success" => true,
            "message" => "Packages fetched successfully",
            "data" => $packages
        ]);
    }

    /**
     * Show single package by slug with full relations.
     * Includes: itineraries, inclusions, faqs, departures, equipment, testimonials, media
     * @param string $slug Package slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        // Find by slug with all needed relations for detail page — now includes tags + pricings (Single/Group) as you requested
        $package = Package::with([
            "category", "destination.region", "region",
            "itineraries", "inclusions", "faqs", "equipment",
            "departures" => fn($q) => $q->where("status","open")->where("departure_date", ">=", now())->orderBy("departure_date"),
            "activities", "addons", "tags", // Tags for SEO as you requested product tags
            "pricings" => fn($q) => $q->where('is_active', true)->orderBy('sort_order'), // Single vs Group pricing tiers
            "testimonials" => fn($q) => $q->where("status","approved")->latest(),
            "media" // Spatie media
        ])->where("slug", $slug)->published()->first();

        if (!$package) {
            return response()->json(["success"=>false, "message"=>"Package not found"], 404);
        }

        // Throttled view increment - 1 per IP per hour to prevent bot inflation
        $viewKey = 'view:package:'.$package->id.':'.request()->ip();
        if (Cache::add($viewKey, 1, 3600)) {
            $package->incrementViews();
        }

        // Get related packages (same category or region, exclude current)
        $related = Package::with(["category","destination"])
            ->where("id","!=",$package->id)
            ->where(function($q) use ($package) {
                $q->where("category_id", $package->category_id)
                  ->orWhere("region_id", $package->region_id);
            })
            ->published()
            ->limit(4)
            ->get();

        return response()->json([
            "success" => true,
            "data" => [
                "package" => $package,
                "related" => $related
            ]
        ]);
    }

    /**
     * Get featured/trending packages for homepage.
     * @return JsonResponse
     */
    public function featured(): JsonResponse
    {
        $featured = Package::with(["category","destination", "tags", "pricings"])
            ->published()->where("featured", true)->orderBy("sort_order")->limit(6)->get();
        $trending = Package::with(["tags", "pricings"])->published()->where("is_trending", true)->limit(6)->get();
        $popular = Package::with(["tags", "pricings"])->published()->where("is_popular", true)->limit(6)->get();

        return response()->json([
            "success" => true,
            "data" => compact("featured","trending","popular")
        ]);
    }

    /**
     * Check availability for a package on a given travel date and pax.
     * Checks fixed departures + group size limits.
     * GET /api/v1/packages/{slug}/availability?travel_date=2026-10-01&pax=2
     */
    public function availability(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'travel_date' => 'required|date|after:today',
            'pax' => 'nullable|integer|min:1|max:30',
        ]);

        $package = Package::with(['departures' => fn($q) => $q->where('status','open')->orderBy('departure_date')])
            ->where('slug', $slug)->published()->first();

        if (!$package) {
            return response()->json(['success'=>false,'message'=>'Package not found'],404);
        }

        $travelDate = $request->input('travel_date');
        $pax = (int) $request->input('pax', 1);

        // Check group size limits
        $groupOk = true;
        $groupMsg = 'OK';
        if ($package->group_size_min && $pax < $package->group_size_min) {
            $groupOk = false;
            $groupMsg = "Minimum {$package->group_size_min} pax required";
        }
        if ($package->group_size_max && $pax > $package->group_size_max) {
            $groupOk = false;
            $groupMsg = "Maximum {$package->group_size_max} pax allowed";
        }

        // Check departures on/near travel date (within 3 days)
        $nearDepartures = $package->departures->filter(function($d) use ($travelDate) {
            $diff = \Carbon\Carbon::parse($d->departure_date)->diffInDays(\Carbon\Carbon::parse($travelDate), false);
            return abs($diff) <= 3;
        })->values();

        // Find exact departure match
        $exact = $package->departures->firstWhere('departure_date', $travelDate);
        $exactAvailable = $exact ? $exact->isAvailable() && $exact->remainingSeats() >= $pax : null;

        // Pricing for pax - find best tier
        $pricingTier = $package->pricings()->where('is_active', true)
            ->where('pax_min', '<=', $pax)
            ->where(function($q) use ($pax) { $q->whereNull('pax_max')->orWhere('pax_max', '>=', $pax); })
            ->orderBy('sort_order')->first();

        $pricePerPerson = $pricingTier ? $pricingTier->price_per_person : $package->finalPrice();
        $total = $pricePerPerson * $pax;

        return response()->json([
            'success' => true,
            'data' => [
                'package' => ['id'=>$package->id, 'slug'=>$package->slug, 'title'=>$package->title, 'group_size_min'=>$package->group_size_min, 'group_size_max'=>$package->group_size_max],
                'travel_date' => $travelDate,
                'pax' => $pax,
                'group_check' => ['ok'=>$groupOk, 'message'=>$groupMsg],
                'pricing_tier' => $pricingTier,
                'price_per_person' => $pricePerPerson,
                'total_price' => $total,
                'departures' => [
                    'exact' => $exact,
                    'exact_available' => $exactAvailable,
                    'nearby' => $nearDepartures,
                    'all_upcoming' => $package->departures->take(5),
                ],
                'is_bookable' => $groupOk && ($exact ? $exactAvailable : true), // if no exact departure, it's a custom date (always bookable if group ok)
            ]
        ]);
    }
}

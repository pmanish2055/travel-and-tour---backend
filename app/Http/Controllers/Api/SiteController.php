<?php
/**
 * File: app/Http/Controllers/Api/SiteController.php
 * Purpose: Central API for all frontend site-wide data: company settings, navigation, stats, sliders, testimonials, team, partners, FAQs, why choose us.
 *          Previously PageController::homepage aggregated some data — this controller expands it into granular + aggregated endpoints.
 *          Routes: GET /api/v1/* (see routes/api.php)
 *          Provides public non-encrypted settings only (no tokens).
 *          Models used: Setting, Slider, Testimonial, TeamMember, Partner, Faq, WhyChooseUs, Package, Destination, etc.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\WhyChooseUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SiteController extends Controller
{
    /**
     * Public site settings (company, seo - non encrypted only).
     * Frontend Header/Footer needs this for dynamic company name, logo, contact.
     * GET /api/v1/settings?group=company
     */
    public function settings(Request $request): JsonResponse
    {
        $request->validate(['group' => 'nullable|string|in:company,seo,general,custom']);
        $group = $request->input('group');
        $cacheKey = 'settings:'.($group ?? 'all');
        $settings = Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = Setting::where('is_encrypted', false);
            if ($group) {
                $query->where('group', $group);
            } else {
                $query->whereIn('group', ['company', 'seo', 'general']);
            }
            return $query->pluck('value', 'key');
        });

        return response()->json(['success' => true, 'data' => $settings]);
    }

    /**
     * Company detail endpoint - cleaner alias for settings company group.
     * GET /api/v1/company
     */
    public function company(): JsonResponse
    {
        $data = Cache::remember('settings:company+seo', 3600, function () {
            return [
                'company' => Setting::where('is_encrypted', false)->where('group', 'company')->pluck('value', 'key'),
                'seo' => Setting::where('is_encrypted', false)->where('group', 'seo')->pluck('value', 'key'),
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Sliders list - hero sliders for homepage.
     * GET /api/v1/sliders
     */
    public function sliders(): JsonResponse
    {
        $sliders = Cache::remember('sliders:active', 3600, fn() => Slider::active()->get());
        return response()->json(['success' => true, 'data' => $sliders]);
    }

    /**
     * Testimonials list with filtering.
     * GET /api/v1/testimonials?featured=1&package_id=1
     */
    public function testimonials(Request $request): JsonResponse
    {
        $query = Testimonial::with('package')->approved()->latest();
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }
        $limit = min((int) $request->input('per_page', 12), 50);
        $testimonials = $query->paginate($limit);
        return response()->json(['success' => true, 'data' => $testimonials]);
    }

    /**
     * Store testimonial/review from frontend (guest can submit).
     * POST /api/v1/testimonials
     */
    public function storeTestimonial(Request $request): JsonResponse
    {
        $data = $request->validate([
            'package_id' => 'nullable|exists:packages,id',
            'customer_name' => 'required|string|max:255',
            'customer_country' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'trip_date' => 'nullable|date',
            'avatar' => 'nullable|string|max:500',
        ]);
        // Force status to pending for moderation
        $data['status'] = 'pending';
        $data['is_featured'] = false;

        $testimonial = Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review! It will be visible after approval.',
            'data' => $testimonial
        ], 201);
    }

    /**
     * Team members.
     * GET /api/v1/team
     */
    public function team(): JsonResponse
    {
        $team = Cache::remember('team:active', 3600, fn() => TeamMember::active()->orderBy('sort_order')->get());
        return response()->json(['success' => true, 'data' => $team]);
    }

    public function partners(): JsonResponse
    {
        $partners = Cache::remember('partners:active', 3600, fn() => Partner::active()->orderBy('sort_order')->get());
        return response()->json(['success' => true, 'data' => $partners]);
    }

    public function faqs(Request $request): JsonResponse
    {
        $category = $request->input('category', 'all');
        $faqs = Cache::remember('faqs:'. $category, 3600, function () use ($request) {
            $query = Faq::active();
            if ($request->filled('category')) $query->where('category', $request->category);
            return $query->get();
        });
        return response()->json(['success' => true, 'data' => $faqs]);
    }

    public function whyChooseUs(): JsonResponse
    {
        $why = Cache::remember('why:active', 3600, fn() => WhyChooseUs::active()->get());
        return response()->json(['success' => true, 'data' => $why]);
    }

    /**
     * Navigation menus - packages by category, regions etc for mega menu.
     * GET /api/v1/navigation
     */
    public function navigation(): JsonResponse
    {
        $data = Cache::remember('navigation', 3600, function () {
            return [
                'categories' => \App\Models\Category::active()->withCount(['packages' => fn($q) => $q->published()])->orderBy('sort_order')->get(['id', 'name', 'slug']),
                'regions' => \App\Models\Region::active()->withCount('destinations')->orderBy('sort_order')->get(['id', 'name', 'slug']),
                'destinations' => \App\Models\Destination::active()->with('region:id,name,slug')->orderBy('sort_order')->limit(15)->get(['id', 'name', 'slug', 'region_id']),
                'pages' => \App\Models\Page::where('status', 'published')->orderBy('title')->get(['id', 'title', 'slug']),
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Site statistics for homepage counters.
     * GET /api/v1/stats
     */
    public function stats(): JsonResponse
    {
        $stats = Cache::remember('site:stats', 600, function () {
            $testimonials = Testimonial::approved()->count();
            return [
                'total_packages' => \App\Models\Package::published()->count(),
                'total_destinations' => \App\Models\Destination::active()->count(),
                'total_bookings' => \App\Models\Booking::count(),
                'total_testimonials' => $testimonials,
                'happy_customers' => $testimonials * 10 + 500,
            ];
        });
        return response()->json(['success' => true, 'data' => $stats]);
    }
}

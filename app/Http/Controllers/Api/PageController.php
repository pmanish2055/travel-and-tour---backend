<?php
/**
 * File: app/Http/Controllers/Api/PageController.php
 * Purpose: CMS pages + sliders + testimonials + team + partners + settings public data.
 *          Routes: GET /api/v1/pages/{slug}, GET /api/v1/sliders, etc.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\TeamMember;
use App\Models\Partner;
use App\Models\Faq;
use App\Models\WhyChooseUs;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /** List all published CMS pages - for sitemap/footer */
    public function index(): JsonResponse
    {
        $pages = Page::where("status","published")->orderBy("title")->get(['id','title','slug','template','seo_title','updated_at']);
        return response()->json(["success"=>true, "data"=>$pages]);
    }

    /** Show CMS page by slug */
    public function show(string $slug): JsonResponse
    {
        $page = Page::where("slug",$slug)->where("status","published")->first();
        if (!$page) return response()->json(["success"=>false,"message"=>"Page not found"],404);
        return response()->json(["success"=>true, "data"=>$page]);
    }

    /** Homepage aggregates - cached 1hr (bust via Company Settings save) */
    public function homepage(): JsonResponse
    {
        $data = Cache::remember('homepage:aggregate', 3600, function () {
            return [
                'sliders' => Slider::active()->get(),
                'testimonials' => Testimonial::approved()->where('is_featured', true)->latest()->limit(6)->get(),
                'team' => TeamMember::active()->limit(6)->get(),
                'partners' => Partner::active()->get(),
                'faqs' => Faq::active()->limit(6)->get(),
                'why' => WhyChooseUs::active()->get(),
                'settings' => Setting::where('is_encrypted', false)->whereIn('group', ['company','seo','general'])->pluck('value', 'key'),
            ];
        });
        return response()->json(['success'=>true, 'data'=>$data]);
    }
}

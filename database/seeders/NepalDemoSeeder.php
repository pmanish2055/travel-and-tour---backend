<?php
/**
 * File: database/seeders/NepalDemoSeeder.php
 * Purpose: Seeds demo data for Nepal Travel & Tour system.
 *          Creates: Regions, Destinations, Categories, Activities, Packages with
 *          Itineraries, Inclusions, FAQs, Departures, Blog posts, Pages, Sliders, Team, etc.
 *          Run via: php artisan db:seed --class=NepalDemoSeeder
 *          Used for: Initial demo after fresh install, testing frontend.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Destination;
use App\Models\Category;
use App\Models\Activity;
use App\Models\Package;
use App\Models\PackageItinerary;
use App\Models\PackageInclusion;
use App\Models\PackageFaq;
use App\Models\PackageDeparture;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\Partner;
use App\Models\Faq;
use App\Models\WhyChooseUs;
use App\Models\Tag;
use App\Models\PackagePricing;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class NepalDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates all demo data in correct order (parents before children).
     */
    public function run(): void
    {
        // === 0. Admin User (for Filament) ===
        $this->command->info("Seeding Admin User...");
        $admin = User::firstOrCreate(
            ["email" => "admin@nepalyatra.com"],
            ["name" => "Admin", "password" => Hash::make("password"), "role" => "super_admin", "is_active" => true, "email_verified_at" => now()]
        );
        // Ensure Shield role exists and assigned (if spatie roles table exists)
        try {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(["name" => "super_admin", "guard_name" => "web"]);
            \Spatie\Permission\Models\Role::firstOrCreate(["name" => "admin", "guard_name" => "web"]);
            \Spatie\Permission\Models\Role::firstOrCreate(["name" => "editor", "guard_name" => "web"]);
            if (!$admin->hasRole("super_admin")) {
                $admin->assignRole("super_admin");
            }
        } catch (\Exception $e) {
            $this->command->warn("Shield role assign skipped: " . $e->getMessage());
        }

        // === 1. Regions: Top-level Himalaya zones ===
        $this->command->info("Seeding Regions...");
        $everest = Region::firstOrCreate(["slug" => "everest-region"], [
            "name" => "Everest Region", "description" => "Home to the world highest peak Mt. Everest (8848m). Trekking in Khumbu valley.",
            "is_featured" => true, "is_active" => true, "sort_order" => 1
        ]);
        $annapurna = Region::firstOrCreate(["slug" => "annapurna-region"], [
            "name" => "Annapurna Region", "description" => "Most popular trekking region with diverse landscapes and Gurung culture.",
            "is_featured" => true, "is_active" => true, "sort_order" => 2
        ]);
        $langtang = Region::firstOrCreate(["slug" => "langtang-region"], [
            "name" => "Langtang Region", "description" => "Close to Kathmandu, Tamang culture, Langtang National Park.",
            "is_featured" => true, "is_active" => true, "sort_order" => 3
        ]);
        $pokhara = Region::firstOrCreate(["slug" => "pokhara-valley"], [
            "name" => "Pokhara Valley", "description" => "City of Lakes, gateway to Annapurna, adventure hub.",
            "is_featured" => true, "is_active" => true, "sort_order" => 4
        ]);
        $chitwan = Region::firstOrCreate(["slug" => "chitwan-region"], [
            "name" => "Chitwan Region", "description" => "Jungle safari, Chitwan National Park UNESCO site.",
            "is_featured" => false, "is_active" => true, "sort_order" => 5
        ]);

        // === 2. Destinations: Specific places within regions ===
        $this->command->info("Seeding Destinations...");
        $ebc = Destination::firstOrCreate(["slug" => "everest-base-camp"], [
            "region_id" => $everest->id, "name" => "Everest Base Camp", "altitude_m" => 5364,
            "overview" => "The ultimate trek to the base of the world highest mountain.", "short_description" => "Iconic trek to EBC at 5364m",
            "is_featured" => true, "is_active" => true
        ]);
        $poonhill = Destination::firstOrCreate(["slug" => "ghorepani-poon-hill"], [
            "region_id" => $annapurna->id, "name" => "Ghorepani Poon Hill", "altitude_m" => 3210,
            "overview" => "Best sunrise viewpoint over Annapurna and Dhaulagiri.", "short_description" => "Famous Poon Hill sunrise 3210m",
            "is_featured" => true, "is_active" => true
        ]);
        $abc = Destination::firstOrCreate(["slug" => "annapurna-base-camp"], [
            "region_id" => $annapurna->id, "name" => "Annapurna Base Camp", "altitude_m" => 4130,
            "overview" => "Sanctuary trek surrounded by Annapurna giants.", "is_featured" => true, "is_active" => true
        ]);
        $phewa = Destination::firstOrCreate(["slug" => "phewa-lake"], [
            "region_id" => $pokhara->id, "name" => "Phewa Lake", "altitude_m" => 827,
            "overview" => "Beautiful lake in Pokhara with Annapurna reflection.", "is_featured" => true, "is_active" => true
        ]);
        $chitwanDest = Destination::firstOrCreate(["slug" => "chitwan-national-park"], [
            "region_id" => $chitwan->id, "name" => "Chitwan National Park", "altitude_m" => 415,
            "overview" => "UNESCO World Heritage jungle safari destination.", "is_featured" => true, "is_active" => true
        ]);

        // === 3. Categories ===
        $this->command->info("Seeding Categories...");
        $catTrek = Category::firstOrCreate(["slug" => "trekking"], ["name" => "Trekking", "icon" => "map", "color" => "#f59e0b", "is_active" => true]);
        $catHike = Category::firstOrCreate(["slug" => "hiking"], ["name" => "Hiking", "icon" => "mountain", "is_active" => true]);
        $catCultural = Category::firstOrCreate(["slug" => "cultural-tour"], ["name" => "Cultural Tour", "icon" => "building", "is_active" => true]);
        $catSafari = Category::firstOrCreate(["slug" => "jungle-safari"], ["name" => "Jungle Safari", "icon" => "paw", "is_active" => true]);
        $catPeak = Category::firstOrCreate(["slug" => "peak-climbing"], ["name" => "Peak Climbing", "icon" => "flag", "is_active" => true]);
        $catHeli = Category::firstOrCreate(["slug" => "helicopter-tour"], ["name" => "Helicopter Tour", "icon" => "helicopter", "is_active" => true]);

        // === 4. Activities ===
        $this->command->info("Seeding Activities...");
        $actTrek = Activity::firstOrCreate(["slug" => "trekking"], ["name" => "Trekking"]);
        $actSight = Activity::firstOrCreate(["slug" => "sightseeing"], ["name" => "Sightseeing"]);
        $actSafari = Activity::firstOrCreate(["slug" => "jungle-safari-activity"], ["name" => "Jungle Safari"]);
        $actRaft = Activity::firstOrCreate(["slug" => "rafting"], ["name" => "Rafting"]);

        // === 5. Packages: Main products ===
        $this->command->info("Seeding Packages...");
        $packages = [
            [
                "title" => "Everest Base Camp Trek - 14 Days",
                "destination_id" => $ebc->id, "region_id" => $everest->id, "category_id" => $catTrek->id,
                "duration_days" => 14, "duration_nights" => 13, "group_size_min" => 2, "group_size_max" => 12, "max_altitude_m" => 5364,
                "difficulty" => "strenuous", "price" => 1500, "discount_price" => 1350, "featured" => true, "is_popular" => true,
                "short_description" => "The classic EBC trek via Namche, Tengboche, Dingboche. Max altitude 5364m.",
                "overview" => "<p>Embark on the legendary Everest Base Camp Trek...</p><p>Includes acclimatization, Sherpa culture, and stunning Himalayan views.</p>",
                "highlights" => ["Trek to Everest Base Camp 5364m", "Kalapatthar sunrise 5545m", "Namche Bazaar Sherpa capital", "Tengboche Monastery"],
                "accommodation" => "Teahouse", "meal_plan" => "B/L/D", "best_season" => ["Spring","Autumn"], "status" => "published"
            ],
            [
                "title" => "Ghorepani Poon Hill Trek - 5 Days",
                "destination_id" => $poonhill->id, "region_id" => $annapurna->id, "category_id" => $catHike->id,
                "duration_days" => 5, "duration_nights" => 4, "group_size_min" => 2, "group_size_max" => 16, "max_altitude_m" => 3210,
                "difficulty" => "easy", "price" => 450, "discount_price" => null, "featured" => true, "is_popular" => true,
                "short_description" => "Short easy trek with best sunrise over Annapurna. Ideal for beginners.",
                "overview" => "<p>Perfect short trek for families and beginners...</p>",
                "highlights" => ["Poon Hill sunrise 3210m", "Ghandruk Gurung village", "Rhododendron forests"],
                "accommodation" => "Teahouse", "meal_plan" => "B/L/D", "best_season" => ["Spring","Autumn","Winter"], "status" => "published"
            ],
            [
                "title" => "Annapurna Base Camp Trek - 7 Days",
                "destination_id" => $abc->id, "region_id" => $annapurna->id, "category_id" => $catTrek->id,
                "duration_days" => 7, "duration_nights" => 6, "group_size_min" => 2, "group_size_max" => 12, "max_altitude_m" => 4130,
                "difficulty" => "moderate", "price" => 750, "discount_price" => 699, "featured" => true,
                "short_description" => "Sanctuary trek to ABC with Machhapuchhre views.",
                "overview" => "<p>Into the heart of Annapurna Sanctuary...</p>",
                "highlights" => ["Annapurna Base Camp 4130m", "Machhapuchhre Base Camp", "Hot springs at Jhinu"],
                "accommodation" => "Teahouse", "meal_plan" => "B/L/D", "best_season" => ["Spring","Autumn"], "status" => "published"
            ],
            [
                "title" => "Pokhara City Tour - 3 Days",
                "destination_id" => $phewa->id, "region_id" => $pokhara->id, "category_id" => $catCultural->id,
                "duration_days" => 3, "duration_nights" => 2, "group_size_min" => 1, "group_size_max" => 20, "max_altitude_m" => 827,
                "difficulty" => "easy", "price" => 200, "featured" => false,
                "short_description" => "Explore Pokhara: lakes, caves, Davis Falls, World Peace Pagoda.",
                "overview" => "<p>Relaxing Pokhara valley tour...</p>",
                "highlights" => ["Phewa Lake boating", "Sarangkot sunrise", "Davis Falls"],
                "accommodation" => "Hotel", "meal_plan" => "B", "best_season" => ["Spring","Autumn","Winter"], "status" => "published"
            ],
            [
                "title" => "Chitwan Jungle Safari - 3 Days",
                "destination_id" => $chitwanDest->id, "region_id" => $chitwan->id, "category_id" => $catSafari->id,
                "duration_days" => 3, "duration_nights" => 2, "group_size_min" => 2, "group_size_max" => 20, "max_altitude_m" => 415,
                "difficulty" => "easy", "price" => 250, "featured" => true,
                "short_description" => "Jungle safari with elephant, jeep, canoe, Tharu culture.",
                "overview" => "<p>Wildlife adventure in Chitwan...</p>",
                "highlights" => ["Jeep safari", "Canoe ride", "Tharu cultural show", "Elephant breeding center"],
                "accommodation" => "Jungle Lodge", "meal_plan" => "B/L/D", "best_season" => ["Winter","Spring"], "status" => "published"
            ],
            [
                "title" => "Muktinath Helicopter Tour - 1 Day",
                "destination_id" => null, "region_id" => $annapurna->id, "category_id" => $catHeli->id,
                "duration_days" => 1, "duration_nights" => 0, "group_size_min" => 1, "group_size_max" => 5, "max_altitude_m" => 3760,
                "difficulty" => "easy", "price" => 1200, "featured" => false,
                "short_description" => "Heli tour to sacred Muktinath temple.",
                "overview" => "<p>Scared pilgrimage by helicopter...</p>",
                "highlights" => ["Muktinath Temple 3760m", "Aerial Himalayan views"],
                "accommodation" => "N/A", "meal_plan" => "N/A", "best_season" => ["Spring","Autumn"], "status" => "published"
            ],
        ];

        foreach ($packages as $data) {
            $slug = Str::slug($data["title"]);
            $pkg = Package::firstOrCreate(["slug" => $slug], array_merge($data, [
                "slug" => $slug,
                "slug" => $slug,
                "seo_title" => $data["title"] . " | Nepal Yatra",
                "seo_description" => $data["short_description"],
                "published_at" => now(),
                "sort_order" => 0
            ]));
            // Attach activities
            $pkg->activities()->syncWithoutDetaching([$actTrek->id, $actSight->id]);

            // Add itineraries if not exists
            if ($pkg->itineraries()->count() === 0) {
                for ($i=1; $i <= $data["duration_days"]; $i++) {
                    PackageItinerary::create([
                        "package_id" => $pkg->id,
                        "day_number" => $i,
                        "title" => "Day $i: " . ($i==1 ? "Arrival in Kathmandu" : ($i==$data["duration_days"] ? "Departure" : "Trek Day $i")),
                        "description" => "Detailed itinerary for day $i of " . $data["title"] . ". Includes walking, meals, overnight details.",
                        "max_altitude_m" => $data["max_altitude_m"] - (rand(0, 500)),
                        "meals" => "B/L/D",
                        "accommodation" => $data["accommodation"] ?? "Teahouse",
                        "overnight_at" => "Location $i",
                        "sort_order" => $i
                    ]);
                }
            }

            // Add inclusions
            if ($pkg->inclusions()->count() === 0) {
                PackageInclusion::create(["package_id"=>$pkg->id, "type"=>"include", "title"=>"Airport transfers", "sort_order"=>1]);
                PackageInclusion::create(["package_id"=>$pkg->id, "type"=>"include", "title"=>"Licensed guide & porter", "sort_order"=>2]);
                PackageInclusion::create(["package_id"=>$pkg->id, "type"=>"include", "title"=>"Accommodation as per itinerary", "sort_order"=>3]);
                PackageInclusion::create(["package_id"=>$pkg->id, "type"=>"exclude", "title"=>"International flights", "sort_order"=>1]);
                PackageInclusion::create(["package_id"=>$pkg->id, "type"=>"exclude", "title"=>"Travel insurance", "sort_order"=>2]);
            }

            // Add FAQs
            if ($pkg->faqs()->count() === 0) {
                PackageFaq::create(["package_id"=>$pkg->id, "question"=>"Do I need TIMS card?", "answer"=>"Yes, TIMS and national park permits are required. We arrange them.", "sort_order"=>1]);
                PackageFaq::create(["package_id"=>$pkg->id, "question"=>"What about altitude sickness?", "answer"=>"We have acclimatization days and guide carries first aid.", "sort_order"=>2]);
            }

            // Add departures (next 3 months)
            if ($pkg->departures()->count() === 0) {
                foreach ([now()->addDays(15), now()->addDays(45), now()->addDays(75)] as $date) {
                    PackageDeparture::create([
                        "package_id"=>$pkg->id,
                        "departure_date"=>$date->toDateString(),
                        "return_date"=>$date->copy()->addDays($data["duration_days"]-1)->toDateString(),
                        "price"=> $data["price"],
                        "seats_total"=>16, "seats_booked"=> rand(0,5), "status"=>"open"
                    ]);
                }
            }
        }

        // === 5.5 Tags & Pricings (as you requested: product tags for SEO + Single/Group pricing) ===
        $this->command->info("Seeding Tags & Pricings...");
        $tagsData = [
            ["name"=>"Family", "color"=>"#10b981", "description"=>"Family friendly"],
            ["name"=>"Adventure", "color"=>"#f59e0b", "description"=>"Adventure"],
            ["name"=>"Budget", "color"=>"#3b82f6", "description"=>"Budget"],
            ["name"=>"Luxury", "color"=>"#8b5cf6", "description"=>"Luxury"],
            ["name"=>"Honeymoon", "color"=>"#ec4899", "description"=>"Honeymoon"],
            ["name"=>"Solo", "color"=>"#6b7280", "description"=>"Solo"],
            ["name"=>"Group", "color"=>"#14b8a6", "description"=>"Group"],
            ["name"=>"EBC", "color"=>"#ef4444", "description"=>"Everest"],
            ["name"=>"Annapurna", "color"=>"#f97316", "description"=>"Annapurna"],
            ["name"=>"Cultural", "color"=>"#6366f1", "description"=>"Cultural"],
        ];
        $tags = [];
        foreach($tagsData as $t){
            $tag = Tag::firstOrCreate(["slug"=>Str::slug($t["name"])], ["name"=>$t["name"], "color"=>$t["color"], "description"=>$t["description"], "is_active"=>true]);
            $tags[] = $tag;
        }
        // Attach 2-3 random tags to each package and create Single/Group pricings
        $allPackages = Package::all();
        foreach($allPackages as $pkg){
            if($pkg->tags()->count() === 0){
                $randTags = collect($tags)->random(rand(2,3))->pluck("id")->toArray();
                $pkg->tags()->sync($randTags);
            }
            if($pkg->pricings()->count() === 0){
                PackagePricing::firstOrCreate(["package_id"=>$pkg->id, "title"=>"Single Traveler"], [
                    "type"=>"single", "pax_min"=>1, "pax_max"=>1, "price_per_person"=>$pkg->price + 200, "currency"=>$pkg->currency, "description"=>"Single traveler private", "is_active"=>true, "sort_order"=>1
                ]);
                PackagePricing::firstOrCreate(["package_id"=>$pkg->id, "title"=>"Group 2-4 Pax"], [
                    "type"=>"group", "pax_min"=>2, "pax_max"=>4, "price_per_person"=>$pkg->finalPrice(), "currency"=>$pkg->currency, "description"=>"Per person for 2-4", "is_active"=>true, "sort_order"=>2
                ]);
                PackagePricing::firstOrCreate(["package_id"=>$pkg->id, "title"=>"Group 5+ Pax"], [
                    "type"=>"group", "pax_min"=>5, "pax_max"=>null, "price_per_person"=>max($pkg->finalPrice() - 100, $pkg->finalPrice()*0.9), "currency"=>$pkg->currency, "description"=>"Per person for 5+ best value", "is_active"=>true, "sort_order"=>3
                ]);
            }
        }

        // === 6. Blog ===
        $this->command->info("Seeding Blog...");
        $catTips = BlogCategory::firstOrCreate(["slug"=>"travel-tips"], ["name"=>"Travel Tips"]);
        $catNews = BlogCategory::firstOrCreate(["slug"=>"news"], ["name"=>"News"]);
        $author = User::first();
        BlogPost::firstOrCreate(["slug"=>"best-time-to-visit-nepal"], [
            "title"=>"Best Time to Visit Nepal", "blog_category_id"=>$catTips->id, "user_id"=>$author->id,
            "excerpt"=>"Discover the ideal seasons for trekking in Nepal.", "content"=>"<p>Spring (Mar-May) and Autumn (Sep-Nov) are best...</p>", "status"=>"published", "published_at"=>now(), "is_featured"=>true
        ]);
        BlogPost::firstOrCreate(["slug"=>"packing-list-for-ebc-trek"], [
            "title"=>"Packing List for EBC Trek", "blog_category_id"=>$catTips->id, "user_id"=>$author->id,
            "excerpt"=>"Essential gear for Everest Base Camp.", "content"=>"<p>Down jacket, trekking boots, etc...</p>", "status"=>"published", "published_at"=>now()
        ]);
        BlogPost::firstOrCreate(["slug"=>"chitwan-jungle-safari-guide"], [
            "title"=>"Chitwan Jungle Safari Guide", "blog_category_id"=>$catNews->id, "user_id"=>$author->id,
            "excerpt"=>"What to expect in Chitwan National Park.", "content"=>"<p>Elephants, rhinos, tigers...</p>", "status"=>"published", "published_at"=>now()
        ]);

        // === 7. CMS ===
        Page::firstOrCreate(["slug"=>"about-us"], ["title"=>"About Us", "content"=>"<h1>About Nepal Yatra</h1><p>Leading tour operator in Nepal...</p>", "status"=>"published"]);
        Page::firstOrCreate(["slug"=>"terms-conditions"], ["title"=>"Terms & Conditions", "content"=>"<p>Terms content...</p>", "status"=>"published"]);
        Page::firstOrCreate(["slug"=>"privacy-policy"], ["title"=>"Privacy Policy", "content"=>"<p>Privacy content...</p>", "status"=>"published"]);
        Slider::firstOrCreate(["title"=>"Discover Nepal Himalayas"], ["subtitle"=>"Trek, Tour, Explore", "image"=>"sliders/himalaya.jpg", "cta_text"=>"View Packages", "cta_link"=>"/packages", "is_active"=>true, "sort_order"=>1]);
        TeamMember::firstOrCreate(["name"=>"Ram Bahadur"], ["designation"=>"Lead Trek Guide", "bio"=>"15 years experience in Everest region", "is_active"=>true]);
        Partner::firstOrCreate(["name"=>"TAAN"], ["logo"=>"partners/taan.png", "website"=>"https://taan.org.np", "is_active"=>true]);
        Partner::firstOrCreate(["name"=>"Nepal Tourism Board"], ["logo"=>"partners/ntb.png", "is_active"=>true]);
        Faq::firstOrCreate(["question"=>"How to book?"], ["answer"=>"You can inquiry via website or WhatsApp.", "category"=>"Booking", "is_active"=>true]);
        WhyChooseUs::firstOrCreate(["title"=>"Expert Local Guides"], ["description"=>"Certified guides with 10+ years experience", "icon"=>"user-group", "is_active"=>true]);
        WhyChooseUs::firstOrCreate(["title"=>"Best Price Guarantee"], ["description"=>"No hidden costs, transparent pricing", "icon"=>"currency-dollar", "is_active"=>true]);
        WhyChooseUs::firstOrCreate(["title"=>"24/7 Support"], ["description"=>"WhatsApp support during your trip", "icon"=>"phone", "is_active"=>true]);

        // === 8. Settings: Company + Tokens ===
        $this->command->info("Seeding Settings with Tokens...");
        $settings = [
            // Company detail
            ["key"=>"company.name", "value"=>"Nepal Yatra Pvt. Ltd.", "group"=>"company", "is_encrypted"=>false, "description"=>"Company legal name"],
            ["key"=>"company.email", "value"=>"info@nepalyatra.com", "group"=>"company", "is_encrypted"=>false],
            ["key"=>"company.phone", "value"=>"+977-1-4440000", "group"=>"company", "is_encrypted"=>false],
            ["key"=>"company.whatsapp", "value"=>"+977-9800000000", "group"=>"company", "is_encrypted"=>false],
            ["key"=>"company.address", "value"=>"Thamel, Kathmandu, Nepal", "group"=>"company", "is_encrypted"=>false],
            ["key"=>"company.pan", "value"=>"123456789", "group"=>"company", "is_encrypted"=>false, "description"=>"PAN/VAT number"],
            ["key"=>"company.reg_no", "value"=>"12345/070/071", "group"=>"company", "is_encrypted"=>false],
            // Tokens (encrypted)
            ["key"=>"tokens.google_map_api_key", "value"=>"AIzaSyDemoKey123456", "group"=>"tokens", "is_encrypted"=>true, "description"=>"Google Maps API for destination maps"],
            ["key"=>"tokens.google_analytics_id", "value"=>"G-XXXXXXXX", "group"=>"tokens", "is_encrypted"=>false],
            ["key"=>"tokens.smtp_host", "value"=>"smtp.mailtrap.io", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.smtp_port", "value"=>"2525", "group"=>"tokens", "is_encrypted"=>false],
            ["key"=>"tokens.smtp_user", "value"=>"demo_user", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.smtp_pass", "value"=>"demo_pass", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.esewa_merchant_code", "value"=>"EPAYTEST", "group"=>"tokens", "is_encrypted"=>true, "description"=>"eSewa merchant code"],
            ["key"=>"tokens.esewa_secret", "value"=>"esewa_secret_demo", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.khalti_public_key", "value"=>"test_public_key", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.khalti_secret", "value"=>"test_secret", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.stripe_publishable", "value"=>"pk_test_demo", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.stripe_secret", "value"=>"sk_test_demo", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.recaptcha_site_key", "value"=>"recaptcha_site_demo", "group"=>"tokens", "is_encrypted"=>false],
            ["key"=>"tokens.recaptcha_secret", "value"=>"recaptcha_secret_demo", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.whatsapp_token", "value"=>"whatsapp_token_demo", "group"=>"tokens", "is_encrypted"=>true],
            ["key"=>"tokens.facebook_pixel_id", "value"=>"1234567890", "group"=>"tokens", "is_encrypted"=>false],
            // SEO
            ["key"=>"seo.site_title", "value"=>"Nepal Yatra - Best Trekking & Tour Operator", "group"=>"seo", "is_encrypted"=>false],
            ["key"=>"seo.meta_description", "value"=>"Book your dream Nepal trek with Nepal Yatra. EBC, Annapurna, Chitwan packages.", "group"=>"seo", "is_encrypted"=>false],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(["key"=>$s["key"]], $s);
        }

        $this->command->info("Demo seeding completed!");
    }
}

# Nepal Yatra - Complete Admin Panel Data Entry Guide
### Step-by-Step from Login to Every Page

> **Project:** Nepal Yatra Tour & Travel (Laravel 11 + Filament 5 Master Backend)  
> **Admin URL:** `http://localhost:8000/admin` (local) or `https://yourdomain.com/admin` (server)  
> **Default Login:** `admin@nepalyatra.com` / `password` (Role: super_admin)  
> **Frontend Base API:** `http://localhost:8000/api/v1` (see `frontend/.env` → `VITE_API_URL`)  
> **Purpose:** This guide helps you enter ALL data via Admin Panel WITHOUT touching code. Follows the exact sidebar order you see after login.

---

## Table of Contents
1. [Quick Start & Login](#1-login--access)
2. [Dashboard Overview](#2-dashboard-overview)
3. [Recommended Data Entry Order (READ FIRST)](#3-recommended-data-entry-order)
4. [STEP A: Company Group (Master)](#step-a-company-group)
   - A1 Company Settings (Master) - 7 Tabs
   - A2 Team Members
   - A3 Partners
   - A4 Pages (CMS)
   - A5 Sliders
   - A6 Why Choose Us
   - A7 FAQs (Global)
5. [STEP B: Tour Management](#step-b-tour-management)
   - B1 Regions
   - B2 Destinations
   - B3 Categories
   - B4 Activities
   - B5 Tags (Product Tags for SEO)
   - B6 Addons
   - B7 Packages (8 Tabs - Master Template)
6. [STEP C: Sales & CRM](#step-c-sales--crm)
   - C1 Bookings
   - C2 Inquiries
   - C3 Custom Trips
   - C4 Payments
   - C5 Coupons
7. [STEP D: Content (Blog)](#step-d-content-blog)
   - D1 Blog Categories
   - D2 Blog Tags
   - D3 Blog Posts (4 Tabs)
8. [STEP E: System](#step-e-system)
   - E1 Users & Roles
   - E2 Settings (Raw Key-Value)
   - E3 Contact Messages
   - E4 Subscribers
9. [STEP F: Verification & Sync MySQL](#step-f-verification)
10. [API Reference for Frontend Developers](#step-g-api-reference)
11. [Tips & Troubleshooting](#tips--troubleshooting)

---

## 1. Login & Access

### 1.1 Start Backend Locally (if not running)
```bash
cd "C:\Users\SJEC\Desktop\my project\tour and travel\backend"
php artisan serve --host=127.0.0.1 --port=8000
# Keep this terminal open. Open new terminal for other commands.
```

### 1.2 Open Admin Panel
- Open browser → `http://localhost:8000/admin` (or `http://127.0.0.1:8000/admin`)
- You will see **Filament login page**: Email + Password fields, Remember checkbox, Sign in button.
- **Screenshot Placeholder:** `docs/images/01-login.png` (Filament dark/light login with Nepal Yatra logo)

### 1.3 Enter Credentials
| Field | Value | Notes |
|-------|-------|-------|
| Email | `admin@nepalyatra.com` | Seeded via `NepalDemoSeeder` |
| Password | `password` | Change immediately after first login |

- Click **Sign in**. On success you land on **Dashboard** (`/admin`).
- If `Invalid credentials`: Run `php artisan make:filament-user` and create new super_admin, or check `php artisan db:seed --class=NepalDemoSeeder` was run.

### 1.4 Change Password (Immediately)
- Top-right avatar → **Profile** or go to **System → Users** → Edit `admin@nepalyatra.com` → Set new password → Save.
- **Important:** Password is hashed; you cannot view old password. If forgotten, run `php artisan make:filament-user` again or tinker: `php artisan tinker` → `\App\Models\User::where('email','admin@nepalyatra.com')->first()->update(['password'=>bcrypt('newpass')]);`

---

## 2. Dashboard Overview

After login you see:
- **Stats widgets:** Total Packages, Bookings, Inquiries, Revenue (if configured). Provided by `app/Filament/Widgets/`
- **Left Sidebar Navigation** grouped exactly as below (top to bottom):
  1. **Company** (first, sort 1) — Master settings
  2. **Tour Management** — Packages, Regions, Destinations, Categories, Activities, Tags
  3. **Sales & CRM** — Bookings, Inquiries, Custom Trips, Payments, Coupons
  4. **Content** — Blog Posts, Blog Categories, Blog Tags
  5. **System** — Users, Roles, Settings, Contact Messages, Subscribers
- **Top bar:** Global search (press `/`), Notifications bell, Avatar menu.
- **Collapsed view on mobile:** Hamburger menu shows same groups.

> **Master Concept:** Every Filament form uses **Tabs (2 columns) + Sidebar Group (1 column)** → 3-column layout. Example: Package form `app/Filament/Resources/Packages/Schemas/PackageForm.php:1` has 8 tabs + sidebar (Publishing & Quick Info). Blog uses same pattern `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php:1`. This uniformity is intentional (as you requested "same structure, group to each and every form").

**Next:** Follow **Section 3 order** — do NOT randomly add packages first. Setup Company → Regions → Destinations → Categories → then Packages, or packages will have no relations to select.

---

## 3. Recommended Data Entry Order

Enter in this exact order to avoid missing relations:

```
ORDER 1: Company → Company Settings (Master)  [one-time rebrand]
ORDER 2: Tour Management → Regions            [e.g., Everest, Annapurna]
ORDER 3: Tour Management → Destinations       [e.g., EBC, Poon Hill - needs Region]
ORDER 4: Tour Management → Categories         [e.g., Trekking, Tour]
ORDER 5: Tour Management → Activities         [e.g., Rafting, Safari]
ORDER 6: Tour Management → Tags               [e.g., Family, Luxury]
ORDER 7: Tour Management → Addons (optional)  [e.g., Porter, Extra Night]
ORDER 8: Company → Sliders, Why Choose Us, FAQs, Team, Partners, Pages  [homepage CMS]
ORDER 9: Tour Management → Packages (8 tabs)  [needs all above]
ORDER 10: Content → Blog Categories → Blog Tags → Blog Posts
ORDER 11: Sales → Coupons (before bookings test)
ORDER 12: Test frontend APIs & Booking flow
```

---

## STEP A: Company Group

> **Location:** Left sidebar → **Company** (icon: Building Office). This group is FIRST in sidebar (`navigationSort = 1`) because it is the Master Setup for any travel site. Changing here rebrands the whole site without code.

### A1. Company Settings (Master) — MOST IMPORTANT — `/admin/company-settings`

**Navigation:** Company → **Company Settings (Master)** (first item, not a resource listing but a Page)  
**File:** `app/Filament/Pages/ManageCompanySettings.php:1`  
**Storage:** `settings` table (`group: company, tokens, seo`) — some keys encrypted (`is_encrypted=true`) via `Setting.php`  
**When to edit:** First thing after login. Before adding any package, set your real company name, logo, contact, MAP, tokens.

**Layout:** 7 Tabs (General, Contact, Branding, Legal, Social, SEO, Tokens & Keys) + Sidebar (Master Setup info + Quick Actions). Each Tab has **Groups/Sections** as you requested.

#### How to Save:
1. Fill fields under each tab (see below).
2. Click top-right **Save Company Settings** button (or Save Company Settings (Master) action).
3. Success notification: "Company settings saved — Master updated"
4. **After save, run** (in terminal) `php artisan app:dump-mysql` to sync `tour_and_travel_mysql.sql` for server if you have MySQL on server. Then `git commit`.

#### Tab 1: General
| Field | Example | Help Text / Notes |
|-------|---------|-------------------|
| Company Legal Name * | `Nepal Yatra Pvt. Ltd.` | Shown in footer, invoices, meta. Master field — change to rebrand e.g., `Bhutan Yatra`. Auto appears in `/api/v1/company` → `company.name` |
| Tagline | `Discover Nepal Himalayas` | Header tagline, SEO |
| Company Description | `Leading tour operator in Nepal for trekking...` | About Us page, SEO. 2-3 sentences. |
| Business Hours | `9AM - 6PM, Sunday - Friday` | Footer & contact page |

#### Tab 2: Contact
| Field | Example | Notes |
|-------|---------|-------|
| Company Email * | `info@nepalyatra.com` | Inquiry notifications go here. Also `/api/v1/homepage` → settings |
| Phone | `+977-1-4440000` | Header, footer tap-to-call |
| WhatsApp | `+977-9800000000` | Floating WhatsApp button (`https://wa.me/977...`) |
| Address * | `Thamel, Kathmandu, Nepal` | Footer & contact page map |
| City | `Kathmandu` | Used for SEO |
| Province | `Bagmati` | |
| Google Map Embed | `<iframe src="https://maps.google...` | Paste full iframe OR leave empty and fill Tokens → Google Map API Key — frontend will generate map using lat/lng from Destinations. Get from `console.cloud.google.com` → Maps Embed API |

#### Tab 3: Branding
| Field | How to Enter | Notes |
|-------|--------------|-------|
| Company Logo | Click **Choose File** → Upload PNG (200×60) | Directory `storage/app/public/company`. Displayed header & admin brand. Delete to use text logo. |
| Favicon | Upload ICO/PNG 32×32 | Browser tab icon |
| Cover Image | Upload 1200×600 hero image | About page hero |
| Primary Color | `#f59e0b` (amber) | Change to rebrand — e.g., `#0ea5e9` for Bhutan (sky blue). Used Tailwind theme via config. |

#### Tab 4: Legal
| Field | Example | Used |
|-------|---------|------|
| PAN / VAT No. | `123456789` | Footer, invoices (Booking PDF) |
| Company Reg No. | `12345/070/071` | Footer |
| TAAN License | `TAAN 1234` | Trekking Agencies Assoc. |
| NTB License | `NTB 5678` | Nepal Tourism Board |

#### Tab 5: Social
All URLs: must be full `https://facebook.com/nepalyatra` (validation `url`). Each shown as footer icon + header.
- Facebook URL
- Instagram URL
- YouTube URL
- LinkedIn URL
- TikTok URL

Leave empty if no account — frontend hides missing.

#### Tab 6: SEO
| Field | Limit | Tip |
|-------|-------|-----|
| Site Title | 60 chars max | `Nepal Yatra - Best Trekking & Tour Operator in Nepal` — Google title |
| Meta Description | 155-160 chars | `Book EBC, Annapurna, Chitwan...` — Google snippet |
| SEO Keywords | Tags input, press Enter | `nepal trek, ebc, annapurna, nepal tour` — also use Product Tags for package SEO |

#### Tab 7: Tokens & Keys (Encrypted)
> **Security:** Fields marked *(encrypted)* are stored with `Crypt::encryptString` via `Setting` model Attribute mutator. Only super_admin should see. Values show as `•••••` with reveal eye icon. Non-empty check drives Payment gateways: `GET /api/v1/payments/methods` reads `Setting::get('tokens.esewa_merchant_code')` etc.

**Sub-sections inside Tokens tab:**

*Google & Analytics:*
- `Google Maps API Key (encrypted)` — Get from Cloud Console → Credentials → API Key → Restrict to Maps Javascript + Geocoding. Needed for Destination lat/lng maps if `map_embed` empty.
- `Google Analytics ID` — `G-XXXXXXXX` — paste in layout `<head>` (frontend reads via `/api/v1/settings`).
- `Facebook Pixel ID` — `1234567890`

*SMTP (Email) — Encrypted:*
- `SMTP Host (encrypted)` — e.g., `smtp.mailtrap.io` or `smtp.gmail.com`
- `SMTP Port` — `2525` or `587`
- `SMTP User (encrypted)` — SMTP username
- `SMTP Pass (encrypted)` — SMTP password / App Password
> After filling SMTP, configure `backend/.env` `MAIL_*` or add observer to set mail dynamically via `Setting::get('tokens.smtp_host')`. Frontend inquiry will then email `company.email`.

*Payment Gateways — Encrypted:*
- `eSewa Merchant Code (encrypted)` — `EPAYTEST` for test, real from eSewa Business
- `eSewa Secret (encrypted)` — Verify API secret
- `Khalti Public Key (encrypted)` — From `khalti.com` dashboard
- `Khalti Secret (encrypted)` — For server verify
- `Stripe Publishable (encrypted)` — `pk_test_...` or `pk_live_...`
- `Stripe Secret (encrypted)` — `sk_test_...` used on server `Stripe::setApiKey(Setting::get('tokens.stripe_secret'))`

*Other Tokens:*
- `reCAPTCHA Site Key` — Demo: `recaptcha_site_demo`
- `reCAPTCHA Secret (encrypted)` — For verifying `POST /api/v1/contact` if you enable.
- `WhatsApp Token (encrypted)` — For WhatsApp Cloud API (if you later send booking via WhatsApp).

**After filling Tokens:** Click Save. Verify encryption: go to **System → Settings** → search `tokens.esewa_secret` — value shows masked but `app/Models/Setting.php:53` accessor decrypts on read.

**API that uses this page:** `GET /api/v1/company` and `GET /api/v1/settings?group=company` return non-encrypted only; tokens are never exposed via API (filter `where is_encrypted=false`). Frontend Header/Footer should fetch via `fetchCompany()` from `frontend/src/services/api.js`.

---

### A2. Team Members — `/admin/team-members`

**Navigation:** Company → Team Members  
**Purpose:** Guides, office staff for About/Team page and `GET /api/v1/team` / `GET /api/v1/homepage` → `team`.  
**File:** `app/Filament/Resources/TeamMembers/TeamMemberResource.php`

**Step to Add Team Member:**
1. Click **New Team Member** (top-right).
2. Tabs: General (2 columns) + Sidebar (Publishing).
   - **TAB General:**
     - **Section Basic — Grouped:** 
       - `Name *` → `John Sherpa`
       - `Designation *` → `Senior Trekking Guide`
       - `Photo *` → FileUpload `team` directory → 400×400 JPG.
       - `Bio` → Textarea → 2-3 lines.
     - **Section Social — Grouped:** `Facebook`, `Instagram`, `LinkedIn` URLs (optional).
   - **SIDEBAR:**
     - `Sort Order` → 0,1,2... (lower = first). Team sorted `orderBy sort_order`.
     - `Is Active` → Toggle ON (inactive hides from `GET /api/v1/team` via `scopeActive`).
3. Click **Create** → Success.

**Manage:** Table shows Name, Designation, Photo, Active toggle inline, Sort order editable. Use search/filter Active. Edit via pencil, delete via trash (soft deletes).

**Frontend:** `Home.jsx` fetches `homepage.team` or `fetchTeam()`; displays grid.

---

### A3. Partners — `/admin/partners`

**Navigation:** Company → Partners  
**Purpose:** TAAN, NTB, Partner logos for footer `partners` carousel. `GET /api/v1/partners`.  
**Fields:**
- `Name *` → `TAAN`
- `Logo *` → FileUpload `partners` → 200×100 PNG transparent
- `Website` → `https://taan.org.np`
- `Sort Order` → 0
- `Is Active` → ON
**Tabs:** General + Sidebar (Publishing). Same pattern.

---

### A4. Pages (CMS) — `/admin/pages`

**Navigation:** Company → Pages  
**Purpose:** Static CMS pages: About Us, Terms & Conditions, Privacy Policy, Contact, etc. Shown via `GET /api/v1/pages/{slug}` and `GET /api/v1/pages` listing for footer Quick Links.  
**Model:** `App\Models\Page` (`is_system` bool for default pages).

**Step to Add/Change Page:**
1. Click New Page.
2. **Tabs:**
   - **General:**
     - `Title *` → `About Us` → Slug auto-generates `about-us` (editable). Route: `/pages/{slug}` on frontend (e.g., `/about-us` reads `GET /api/v1/pages/about-us`).
     - `Content *` → RichEditor → Full HTML content (about text, terms). Write as rich text; supports headings, lists, images.
     - `Template` → Select `default` or `about` (if custom blade).
   - **SEO:**
     - `SEO Title` → 60 chars
     - `SEO Description` → 160 chars
   - **Sidebar:**
     - `Status` → Select `published` (visible) / `draft` (hidden) — `scopePublished` filters.
     - `Is System` → Toggle (system pages cannot be deleted via UI precaution).

**Seeded defaults:** Check `NepalDemoSeeder` — About Us, Terms, Privacy are pre-seeded. Edit them after login rather than create new.

**Frontend use:** Footer Quick Links loop `pages` from `fetchNavigation()` or `fetchPages()`; clicking goes to generic `Page.jsx` that fetches `fetchPageBySlug(slug)`.

---

### A5. Sliders — `/admin/sliders`

**Navigation:** Company → Sliders  
**Purpose:** Homepage hero slider (3-5 slides). `GET /api/v1/sliders` and `GET /api/v1/homepage` → `sliders`.  

**Step:**
1. New Slider → Tab General (2 columns) + Sidebar.
2. **Fields:**
   - `Title *` → `Everest Base Camp Trek`
   - `Subtitle` → `14 Days - World’s Highest Peak`
   - `Image *` → FileUpload (1920×800 recommended, ≤2MB)
   - `CTA Text` → `View Package` (button text)
   - `CTA Link` → `/packages/everest-base-camp-trek-14-days` or `/packages`
   - `Sort Order *` → 0,1...
   - `Is Active *` → ON (inactive hidden via `scopeActive()`).
3. Create. Drag sort order via table reorder.

**Frontend:** `Home.jsx` hero slider loops `sliders`; each slide shows `image` as background, `title`, `cta_link` button.

---

### A6. Why Choose Us — `/admin/why-choose-us`

**Navigation:** Company → Why Choose Us  
**Purpose:** 3-6 feature items on homepage "Why Choose Nepal Yatra" grid. `GET /api/v1/why-choose-us` + homepage `why`.

**Fields (Tabs General + Sidebar):**
- `Title *` → `Expert Local Guides` (with icon placeholder in frontend `?` could be heroicon name if you add)
- `Description` → `Certified 10+ years experience...`
- `Icon` → e.g., `shield-check` or emoji — Shown as `div` with `icon`.
- `Sort Order` → 0
- `Is Active` → ON

**Example seeded:**
- Expert Guides, Best Price, 24/7 Support. Edit these after login.

---

### A7. FAQs (Global) — `/admin/faqs`

**Navigation:** Company → FAQs  
**Note:** This is GLOBAL FAQ for footer/FAQ page, NOT package-specific FAQs (those are inside Package → FAQ tab repeater). Global FAQ `GET /api/v1/faqs?category=booking` + homepage limited 6.

**Fields:**
- `Question *` → `Do I need TIMS card?`
- `Answer *` → `Yes, required for trekking...`
- `Category` → `booking` / `payment` / `general` (used for filter)
- `Sort Order` → 0
- `Is Active` → ON

**Tabs:** Single General tab + Sidebar Publishing.

---

## STEP B: Tour Management

> **Group:** Tour Management — Central for Packages. Must follow hierarchical order: Region → Destination → Category → Activities/Tags/Addons → Packages last.

### B1. Regions — `/admin/regions`

**Purpose:** Top-level geography hierarchy (Everest, Annapurna, Langtang, Chitwan). Package has `region_id` denormalized for fast filtering. Destinations belong to Region. Provides `GET /api/v1/regions` and `GET /api/v1/regions/{slug}` with packages.  
**Model:** `App\Models\Region` (`parent_id` hierarchical via self-relation, `is_featured`, `is_active`).

**Step to Add Region:**
1. **New Region** → 3-column layout: Tabs (2) + Sidebar (1)
   - **TAB General:**
     - `Section Basic — Grouped` (2 columns):
       - `Parent Region` → Select (searchable) existing region or leave empty for top-level. E.g., `Khumbu` parent is `Everest Region`.
       - `Name *` → `Everest Region`
       - `Slug *` → auto `everest-region` (unique). Editable.
       - `Description` → 2-3 sentences overview
       - `Featured Image` → FileUpload `regions`
   - **TAB Media/Branding:**
     - Same image reorder but extra SEO fields inside.
   - **TAB SEO:**
     - `SEO Title` → 60 chars
     - `SEO Description` → 160 chars
     - `Sort Order` → 0
   - **SIDEBAR:**
     - `Is Featured` → Toggle (homepage region grid)
     - `Is Active` → ON (scopeActive)

2. Save.

**Table:** Shows Name, Parent, Destinations count (`withCount`), Sort Order inline edit. Filter Active.

**API:** `fetchRegions({ with_destinations:1 })` returns regions with `destinations` preview. Region detail shows packages paginated.

**Tip:** Create 4-6 regions max (Everest, Annapurna, Langtang, Manaslu, Chitwan, Kathmandu). Keep hierarchical shallow: 1 parent + children.

---

### B2. Destinations — `/admin/destinations`

**Purpose:** Specific places within Region (EBC, Poon Hill, Phewa Lake). Package has `destination_id`. Filter via `GET /api/v1/packages?destination=poon-hill` or `GET /api/v1/destinations/{slug}` shows packages limit 6.  
**Fields per Tab (see `DestinationForm.php`):**

**Step:**
1. New Destination → Tabs (2) + Sidebar (1)
   - **TAB General (icon map-pin):**
     - `Region *` → Select searchable (must exist from B1) — relation.
     - `Destination Name *` → `Ghorepani Poon Hill` (slug auto)
     - `Slug *` → Unique.
     - `Overview` → RichTextarea → Long description (detail page).
     - `Short Description` → 500 chars max → Card excerpt.
   - **TAB Geo & Media (globe-alt):**
     - **Section Geography:**
       - `Altitude (m)` → `3210`
       - `Latitude` → `28.1234567` (7 decimals, used for map via `tokens.google_map_api_key`)
       - `Longitude` → `83.1234567`
     - **Section Media — Grouped:**
       - `Featured Image` → FileUpload `destinations`
       - `Video URL` → YouTube link
       - `Gallery Images` → Multiple FileUpload `destinations/gallery`
       - `Map Embed` → Textarea for iframe if you want specific map; else auto from lat/lng.
       - `Best Season` → TagsInput (press Enter: `Spring`, `Autumn`)
   - **TAB SEO:**
     - `SEO Title`, `SEO Description`, `Sort Order`
   - **SIDEBAR:**
     - `Is Featured` → ON for homepage destination grid
     - `Is Active` → ON

2. Save.

**Manage:** Table `DestinationsTable.php` shows Image, Name, Region, Altitude, Featured. Bulk actions.

**Frontend:** Destinations page grid fetches `fetchDestinations()`; click goes to destination detail showing packages via `fetchDestinationBySlug`.

---

### B3. Categories — `/admin/categories`

**Purpose:** Tour types (Trekking, Cultural Tour, Peak Climbing, Helicopter Tour). Package `category_id` required for filtering. `GET /api/v1/categories` with `packages_count`, `GET /api/v1/categories/{slug}` with packages paginated.  
**Fields:**
- `Name *` → `Trekking`
- `Slug *` → auto
- `Description` → Brief
- `Icon` → e.g., `mountain`
- `Color` → hex `#f59e0b`
- `Featured Image` → FileUpload `categories` (for category hero)
- `Sort Order` → 0
- `Is Active` → ON
- `SEO Title`, `SEO Description`

**Tabs:** General + Media/Branding + SEO + Sidebar same pattern.

**Flow:** Create 5-6 categories before packages.

---

### B4. Activities — `/admin/activities`

**Purpose:** Many-to-many with Package via `activity_package` pivot. Activities also act like tags: e.g., Rafting, Paragliding, Jungle Safari, Sightseeing, Cultural Tour. Package form Select `activities` (multiple). Filter packages via `activities` relation maybe future. `GET /api/v1/activities`.  
**Fields:**
- `Name *` → `Rafting`
- `Slug *` → auto
- `Icon` → heroicon
- `Description` → Brief
- `Is Active` → ON

**Steps:** New Activity → General tab (2 cols) + Sidebar → Fill → Save. Same pattern but simpler.

**Linking to Package:** Inside Package → General tab bottom `Activities (Tags)` → Select multiple.

---

### B5. Tags (Product Tags for SEO) — `/admin/tags`

**Purpose:** Product tags for SEO and filtering as you requested. E.g., Family, Adventure, Budget, Luxury, Honeymoon, Solo, Group, EBC, Annapurna. M2M `package_tag`. Package SEO tab → Select `tags`. Provides `GET /api/v1/tags` and `GET /api/v1/tags/{slug}` with packages; also `PackageController index` filter `?tag=adventure` queries `whereHas tags slug`.  
**Fields:**
- `Name *` → `Adventure`
- `Slug *` → auto (lower)
- `Color` → `#f59e0b` (badge color)
- `Description` → SEO description
- `Is Active` → ON

**Step:** New Tag → Fill → Save. Create at least: Adventure, Family, Budget, Luxury, Honeymoon, Solo, Group.

**Using:** Package → SEO & Tags tab → `Product Tags` Select with `createOptionForm` — you can create new tags inline while editing package (tag name → auto slug → color). Also `TagForm` itself.

---

### B6. Addons — `/admin/addons`

**Purpose:** Extra purchasable services M2M `addon_package`. E.g., Porter, Extra Night Accommodation, Helicopter Return, Travel Insurance. Displayed in Package detail checkout as checkboxes; booking total calc can include. `GET /api/v1/addons` + `GET /api/v1/packages/{slug}/addons` + `GET /api/v1/addons/{slug}`.  
**Fields:**
- `Name *` → `Extra Porter`
- `Slug *` → auto
- `Description` → `Helper for luggage`
- `Price *` → `25.00`
- `Price Type` → `per_day` / `per_person` / `fixed`
- `Icon` → heroicon
- `Sort Order` → 0
- `Is Active` → ON

**Step:** New Addon → General tab → Fill → Save.

**Linking:** Package → SEO & Tags tab bottom `Addons (Extra Services)` → Select multiple.

---

### B7. Packages (Master - 8 Tabs) — `/admin/packages` — MOST COMPLEX

**Purpose:** Central model `App\Models\Package` — represents a tour. 49 columns, 8 tabs you requested, plus 20 Sections/Groups + Sidebar. Everything inside Package (no separate menus for Itinerary etc — now repeaters inside). Provides `GET /api/v1/packages`, `featured`, `{slug}` with all relations, `availability`.  
**File:** `app/Filament/Resources/Packages/Schemas/PackageForm.php:1` (702 lines)  
**Related tables:** `package_itineraries`, `package_inclusions` (include/exclude), `package_faqs`, `package_departures`, `package_pricings`, `package_equipment`, `media` (Spatie gallery), `activity_package`, `addon_package`, `package_tag`, etc.  
**Table:** `PackagesTable` shows Title, Image, Category, Destination, Duration, Price, Featured toggles, Status badge, Views.

#### Recommended Quick Start vs Full:
- **Quick Start (Min viable):** Fill only Required fields + General tab + Base Pricing + Publish → Save → Test API.
- **Full (Production):** Fill all 8 tabs as below.

#### Step-by-Step to Add Package (Complete):

**ACTION: Click New Package → You see 3-column layout: Tabs (2) + Sidebar (1).**

##### TAB 1: General Details (icon information-circle)

*Section Basic Information (2 columns):*
- `Package Title *` → `Everest Base Camp Trek - 14 Days` (max 255). **Helper:** Auto-generates slug if empty; title displayed on cards/detail.
  - Type title → slug auto suggests `everest-base-camp-trek-14-days` (see `Package.php booted`).
- `URL Slug *` → `everest-base-camp-trek-14-days` (unique validation `ignoreRecord`). Used `GET /api/v1/packages/{slug}` and frontend route `/packages/:slug`. Must be lowercase hyphen.
- `Category *` → Select searchable preload → Choose e.g., `Trekking`. Determines filtering `?category=trekking`.
- `Primary Destination` → Select searchable → `Everest Base Camp`.
- `Region` → Select → `Everest Region`. Denormalized for `?region=everest-region`.
- `Duration (Days) *` → `14` numeric ≥1. Shown `14 Days`.
- `Duration (Nights) *` → `13`
- `Min Group Size` → `2` (min pax for group departure validation)
- `Max Group Size` → `16` (also for availability check)
- `Max Altitude (m)` → `5364`
- `Difficulty *` → Select: `easy`, `moderate`, `hard`, `strenuous`, `challenging` → Determines badge color and filter `?difficulty=hard`.
- `Trip Type *` → `private` / `fixed_departure` / `daily` → If `fixed_departure` then Departures tab must be filled.

*Section Trip Specifics (2 columns):*
- `Accommodation` → `Teahouse + Hotel` (placeholder shown).
- `Meal Plan` → `B/L/D` (Breakfast etc)
- `Transportation` → `Private vehicle, Domestic flight` (full width).
- `Best Season` → TagsInput → Type `Spring` press Enter, `Autumn` — Stored as JSON array `["Spring","Autumn"]`.
- `Overview (Rich Text)` → RichEditor full width → Long HTML description (moved from sidebar for width). Supports bold, lists, links, images. Shown in detail Tabs → Overview via `dangerouslySetInnerHTML`.
- `Activities (Tags)` → Select multiple preload → e.g., `Trekking`, `Sightseeing` — M2M.

##### TAB 2: Itinerary (icon map)

*Section Day-wise Itinerary:*
- **Repeater `itineraries` → relationship `itineraries` (HasMany ordered by sort_order). Features: reorderable drag, collapsible, collapsed by default, itemLabel `Day X: Title`.**
- Click **Add Day** → Each day fields:
  - `Day No. *` → `1` numeric
  - `Day Title *` → `Arrival in Kathmandu` (columnSpan 2)
  - `Day Description *` → Textarea 3 rows → `Arrive, transfer to hotel...`
  - Grid 3: `Altitude (m)` → `1400`, `Meals` → `D`, `Accommodation` → `Hotel`
  - Grid 2: `Overnight At` → `Kathmandu`, `Walking Hours` → `2-3`
- Add 14 days drag to reorder. **Helper:** Previously separate Itinerary menu, now tab inside Package.

##### TAB 3: Includes / Excludes (icon check-circle)

*Section What is Included (green check):*
- Repeater `includes` → filtered relation `includes()` type=include → fields: `Include Title *` → `All ground transportation`, `Icon` → `check`, Hidden `type=include`. Shows green checklist frontend.

*Section What is Excluded (red cross):*
- Repeater `excludes` → relation `excludes()` type=exclude → `International flights` etc.

*Advanced (collapsed):* Repeater `inclusions` → both types with `Select type include/exclude` + `title` + `description` → Power user.

##### TAB 4: Departures (icon calendar)

- Repeater `departures` → relationship `departures` → For `fixed_departure` trips. Leave empty for `private` tours.
- Each:
  - Grid 3: `Departure Date *` → DateTimePicker native false → `2026-10-01`, `Return Date *` → `2026-10-14`, `Price (Override)` → `1350` or empty to use package base price.
  - Grid 3: `Total Seats` → `16`, `Booked Seats` → `0` (increments via `BookingController` `increment seats_booked`), `Status` → Select `open`/`guaranteed`/`closed`/`cancelled` default `open`.
  - `Note` → `Festival departure` full width.
- ItemLabel `departure_date - status`. Reorderable collapsible.
- **Helper:** Bookings via `POST /api/v1/bookings` with `departure_id` will increment `seats_booked`. Availability endpoint `GET /packages/{slug}/availability?travel_date=2026-10-01&pax=2` checks this.

##### TAB 5: Pricing (icon currency-dollar)

*Section Base Pricing (3 columns):*
- `Base Price (per person) *` → `1350` prefix $ numeric — Main listing price.
- `Discount Price` → `1200` — If set shows sale badge & discount %.
- `Currency *` → `USD` or `NPR` default USD.
- `Price Type` → `per_person` or `per_group`
- `Price on Request?` → Toggle — If ON frontend shows "Price on Request" hides price.

*Section Pricing Tiers — Single vs Group (as you requested):*
- Repeater `pricings` → relationship `pricings` orderColumn sort_order reorderable collapsible itemLabel `Title - $price_per_person`.
- Each tier Grid 3:
  - `Tier Title *` → `Single Traveler` / `Group 2-4 Pax`
  - `Type *` → `single`/`group`/`private`/`fixed` default `group`
  - `Currency` → `USD`
- Grid 3:
  - `Min Pax *` → `1` or `2`
  - `Max Pax` → `1` or `4` empty for unlimited
  - `Price per Person *` → `1800` for single, `1350` for group
- Grid 2:
  - `Total Group Price (optional)` → `2700` if per_group type
  - `Active?` → Toggle default true
- `Description` → Textarea full width → `Includes guide, etc.`
- **Helper:** Frontend shows pricing table via `package.pricings`. Availability endpoint picks correct tier via `pax_min <= pax <= pax_max`.

##### TAB 6: FAQs & Equipment (icon question-mark)

*Section FAQs:*
- Repeater `faqs` → relationship → `Question *` → `Do I need TIMS?` full width, `Answer *` → Textarea 3 rows accordion.

*Section Equipment / Gear List:*
- Repeater `equipment` → relationship → Grid 3: `Item *` → `Down jacket`, `Description` → `Warm -10°C rating`, `Required?` → Toggle default true.

##### TAB 7: Gallery (icon photo)

*Section Featured Image:*
- `Featured Image` → FileUpload `packages/featured` image → Main card/detail header 1200×800.

*Section Gallery Images:*
- `Gallery (Spatie Media)` → FileUpload multiple image `packages/gallery` → 5-10 images slider via `package.media` (Spatie collection `gallery`). Note comment: replace with `SpatieMediaLibraryFileUpload` if using Spatie UI.

##### TAB 8: SEO & Tags (icon globe-alt) — Product Tags as requested

*Section SEO Settings (2 columns):*
- `SEO Title` → `Everest Base Camp Trek 14 Days | Nepal Yatra` max 60 chars Google title
- `SEO Keywords` → `everest trek, nepal trek, ebc` comma
- `SEO Description` → Textarea 3 rows max 160 chars snippet full width

*Section Product Tags (for SEO as you requested):*
- `Product Tags` → Select multiple `tags` relationship preload searchable with **createOptionForm** inline: `Name *`, `Slug`, `Color` hex — Used filtering `?tag=adventure` and SEO badges. Select e.g., `Adventure`, `Budget`, `Luxury`.
- `Activities (Also Tags)` → Select multiple also tags but separate relation `activities` → Select `Trekking`
- `Addons (Extra Services)` → Select multiple `addons` → e.g., `Porter`

*Section Highlights:*
- `Highlights` → TagsInput → Press Enter each bullet: `Trek to EBC 5364m`, `Views of Everest`, `Sherpa culture` — Checklist shown detail.

##### SIDEBAR: Publishing & Sidebar (Group as you requested — sticky right 1 column)

*Section Publishing & Sidebar:*
- `Status *` → Select `draft` / `published` / `archived` default `draft` → **Only published shows via API** `scopePublished`. Set to `published` after completing all tabs to make visible on frontend `GET /api/v1/packages`. Draft hides.
- `Publish Date` → DateTimePicker — Leave empty for immediate; if status published but date empty, booted sets `published_at=now()`.
- `Featured?` → Toggle → Homepage featured via `?featured=1` or `GET /packages/featured`
- `Trending?` → Toggle → `is_trending` carousel
- `Popular?` → Toggle → `is_popular` list
- `Sort Order` → Numeric 0 default — Lower appears first `orderBy sort_order`
- `Views` → Numeric 0 — Auto-increments on detail via `incrementViews()`, used popularity sorting.

*Section Quick Info (collapsible):*
- Placeholder Tip: "Edit slug in General Details tab. URL: /packages/{slug}" — helper.

**SAVE:** Bottom **Create** or **Save Changes**. Validation ensures required fields. On success package appears in table.

**After Creating Package:**
- Test API: `curl http://localhost:8000/api/v1/packages/everest-base-camp-trek-14-days` should return full JSON with relations.
- Check Frontend: `http://localhost:5173/packages` → card appears. `http://localhost:5173/packages/everest-base-camp-trek-14-days` → detail with tabs.
- If not appearing: Check `Status=published` — draft hidden.

**Editing Packages:** Pencil icon → same tabs → change — save.

**Bulk Actions:** Table has bulk delete (soft). Use `Toggle` columns inline for featured quickly.

---

## STEP C: Sales & CRM

> **Group:** Sales & CRM — Handles orders & leads from frontend forms. These are NOT manually created (except test) but are generated via `POST` APIs. Admin reviews, updates status, assigns, marks paid.

### C1. Bookings — `/admin/bookings`

**Purpose:** Stores confirmed booking `bookings` + `booking_travelers` for permits. Workflow `pending → confirmed → completed` + `payment_status unpaid → paid`. Generates `booking_code` e.g., `NPL-2026-ABCDEF` via `Booking.php booted`. Increment departure seats. `GET /api/v1/bookings/{code}` for tracking, `POST /api/v1/bookings` from frontend.  
**Model:** `Booking` fillable: `booking_code, user_id, package_id, departure_id, travel_date, pax_adult, pax_child, total_amount, advance_amount, payment_status, booking_status, special_request, source, customer_name, customer_email, customer_phone, customer_country, cancelled_at`.

**Admin Use:**
- **Table:** Shows Booking Code, Package, Customer Name/Email/Phone, Travel Date, Pax (adult+child), Total Amount, Advance, Payment Status badge, Booking Status badge, Created At. Filters: Status, Payment Status, Date.
- **Create (Test only):** New Booking → Tabs: General (customer, package, travel_date, pax), Travelers Repeater (full_name, passport_no, nationality, dob, gender, is_lead), Payments. Usually use frontend `POST /api/v1/bookings` to test.
- **Edit:** Click row → Change `booking_status` to confirm, `payment_status` to paid, `advance_amount`.
- **Relations:** Inline Travelers Repeater (for TIMS), Payments HasMany.

**Frontend flow:** PackageDetail → "Check Availability" → Booking form → `createBooking({ package_id, travel_date, pax_adult, customer_* , travelers: [{full_name,nationality}] })` → 201 returns `{ success, message:"Booking created... code: NPL-...", data: booking with package, travelers }`. Customer can track via `fetchBookingByCode(code)` on custom page `/bookings/{code}` you may create.

**Availability:** Package detail `availability` endpoint logic uses `group_size_min/max` and `departures` remaining seats.

---

### C2. Inquiries — `/admin/inquiries`

**Purpose:** Package inquiry/lead (simpler than booking). Captures interest via `POST /api/v1/inquiries` (PackageDetail Inquiry form) with `package_id`, `name,email,phone,country,travel_date,pax,message`. Assign to staff, status workflow `new → contacted → converted → closed`.  
**Fields:** `package_id` (nullable), `name`, `email`, `phone`, `country`, `travel_date`, `pax`, `message`, `status`, `assigned_to` (user FK).

**Admin:**
- Table shows Name, Email, Phone, Package Title, Travel Date, Pax, Status badge, Assigned To avatar, Created.
- New Inquiry → Form fields same.
- Edit → Update `status` dropdown, assign user.
- Bulk mark contacted.

**Frontend:** PackageDetail → Inquiry form at bottom `handleInquiry()` → `sendInquiry({ package_id: pkg.id, name, email, phone, message })` → Notification "Inquiry sent!"

---

### C3. Custom Trips — `/admin/custom-trips`

**Purpose:** Build-your-own-trip request via `POST /api/v1/custom-trips` (CustomTrip page). Fields broader budget/interests. `GET` none; admin reviews.  
**Fields:** `name, email, phone, country, destination_interest, duration_days, budget, travel_date, pax, interests, message, status`.

**Admin:** Table Inquiry-like but with budget, interests. Edit status.

**Frontend:** `/custom-trip` placeholder page you should expand: form with those fields → `sendCustomTrip(data)`.

---

### C4. Payments — `/admin/payments`

**Purpose:** Tracks gateway payments `payments` table: `booking_id, gateway, transaction_id, amount, currency, status, raw_response JSON`. Relation to Booking. Backend ready for eSewa/Khalti/Stripe/Bank. `GET /api/v1/payments/methods`, `POST /api/v1/payments/initiate`, `verify`.  
**Admin:** List shows Booking Code (via relation), Gateway badge, Amount, Transaction ID, Status. Edit to manually mark `completed` if bank transfer proof verified outside. Create Test Payment.

**Flow:** Booking `pending → unpaid` → frontend calls `initiatePayment({ booking_code, gateway: 'esewa', amount })` → receives `gateway_data.esewa_form_data` to submit to eSewa. After redirect, frontend calls `verifyPayment({ booking_code, gateway, transaction_id: refId, status: 'completed' })` → updates `payment_status=paid`, `booking_status=confirmed`.

**Tokens:** Ensure Company Settings → Tokens filled; `methods` endpoint reports `enabled`.

---

### C5. Coupons — `/admin/coupons`

**Purpose:** Discount codes `coupons` + pivot `coupon_package` (restricted packages). Fields: `code`, `discount_type fixed/percent`, `value`, `valid_from`, `valid_to`, `usage_limit`, `used_count`, `is_active`. Validate via `POST /api/v1/coupons/validate`.  
**Admin:**
- New Coupon:
  - `Code *` → `NEPAL10` uppercase unique
  - `Discount Type *` → `percent` or `fixed`
  - `Value *` → `10` (means 10% or $10)
  - `Valid From` → Date or empty always valid from now
  - `Valid To` → `2026-12-31`
  - `Usage Limit` → `100` null unlimited
  - `Used Count` → 0 (increments on booking with coupon — implement manually)
  - `Is Active *` → ON
  - `Applies to Packages` → Select multiple if restricted; leave empty for all packages.
- Table shows Code, Type, Value, Validity, Used/Limit, Active.

**Frontend:** Booking checkout input `Enter coupon code` → call `validateCoupon({ code: "NEPAL10", package_id, amount: 1350 })` → returns `discount`, `final_amount` to display; on booking create, send `coupon_code` (add field custom) and backend should apply (extend `BookingController` to handle coupon).

---

## STEP D: Content (Blog)

> **Group:** Content — Blog for SEO. Similar master Tabs/Groups/Sidebar pattern as Package (you requested "Blog same as Package").

### D1. Blog Categories — `/admin/blog-categories`

**Purpose:** Blog grouping `blog_categories` → like `Trekking Tips`, `Culture`, `News`. BlogPost `blog_category_id`. `GET /api/v1/blog-categories` with posts count.  
**Fields:**
- `Name *` → `Trekking Tips`
- `Slug *` → auto
- `Description` → Brief
- `Is Active?` maybe (check form)
- Tabs: General + Sidebar (same group pattern) — `BlogCategoryForm.php` uses Tabs 3 + Sidebar.

**Step:** New → Fill → Save. Create 3-4 categories before posts.

---

### D2. Blog Tags — `/admin/blog-tags`

**Purpose:** Blog tags M2M `blog_post_tag` separate from product tags. `GET /api/v1/blog-tags` listing.  
**Fields:**
- `Name *` → `Everest`
- `Slug *` → auto
- Similar sidebar.

---

### D3. Blog Posts — `/admin/blog-posts` — 4 Tabs Master

**Purpose:** Article `blog_posts` with author, category, tags, media. `GET /api/v1/blogs`, `featured`, `{slug}` with related, `GET /api/v1/blogs?category=slug&search=`.  
**File:** `app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php:1` — 4 tabs (General, Content, Media, SEO & Tags) + Sidebar Publishing — same as Package master you requested.

**Step to Add Post:**
1. New Blog Post → Tabs (2) + Sidebar (1)
   - **TAB General:**
     - `Section Basic — Grouped` (2 cols):
       - `Title *` → `Top 10 Tips for EBC Trek`
       - `Slug *` → auto
       - `Blog Category *` → Select `Trekking Tips`
       - `Author` → Select User (auto current user if hidden)
       - `Excerpt` → Textarea for listing snippet.
   - **TAB Content (icon document):**
     - `Content *` → RichEditor full → Article body with headings, images.
     - `Tags` → Select multiple `tags` relationship.
   - **TAB Media:**
     - `Featured Image *` → FileUpload `blogs` image (1200×600)
     - `Gallery` → Multiple Spatie media if needed.
   - **TAB SEO & Tags:**
     - `SEO Title` → 60 chars
     - `SEO Description` → 160 chars
     - `Is Featured` → Toggle for `GET /blogs/featured` homepage
     - `Status` → `published`/`draft` (scopePublished)
     - `Published At` → DateTime
     - `View Count` → 0
   - **SIDEBAR Publishing:**
     - `Status` select `published`
     - `Published At` picker
     - `Is Featured` toggle
     - `View Count`
     - Quick Info placeholder: `Blog will be at /blogs/{slug}`

2. Save. Test: `curl http://localhost:8000/api/v1/blogs/top-10-tips-for-ebc-trek`.

**Manage:** Table shows Title, Category, Image, Views, Status, Featured.

---

## STEP E: System

> **Group:** System — Users & raw configs.

### E1. Users — `/admin/users`

**Purpose:** `users` table + Spatie Permission (Shield). Roles: `super_admin` (all), `admin`, etc.  
**Fields:** `Name *`, `Email *` unique, `Password *` (hashed, only on create/edit if filled), `Roles` → Select multiple via Shield (checkbox).  
**Admin:** Create New User → Assign role. Table shows roles badges. Edit to reset password.

**Login user relevance:** Any user with role containing `can access Filament` can login `/admin`. Check Roles resource if you use Shield: `/admin/shield/roles` → permissions per resource (manage Package, Booking etc).

**Command to create:** `php artisan make:filament-user` interactive prompts Email/Password/Name → `super_admin` override via `fix_roles.php`.

---

### E2. Roles (Shield) — `/admin/shield/roles` (if visible)

**Purpose:** Filament Shield permission matrix. After installing Shield, run `php artisan shield:generate --all` to generate permissions per Resource.  
**Use:** Create roles like `Editor` (can edit Blog but not Payments), `Sales` (Bookings only). Assign at Users.

---

### E3. Settings (Raw Key-Value) — `/admin/settings` (if table name `settings`)

**Purpose:** Raw browsing of `settings` table (`group, key, value, is_encrypted, description`). This is DB view behind Company Settings Page. **Prefer Company Settings Page** for editing; use this raw only for debugging or adding custom keys.  
**Shows:** Key, Value (decrypted if not encrypted; encrypted shows encrypted string but model accessor decrypts), Group, Encrypted flag.

**Add Custom Setting:** New → `Group` → `general`, `Key` → `custom.hello`, `Value` → `world`, `Is Encrypted` → OFF, `Description` → Help → Save. Retrieve via `Setting::get('custom.hello')`.

---

### E4. Contact Messages — `/admin/contact-messages`

**Purpose:** `contact_messages` from `POST /api/v1/contact` (frontend Contact page form). Admin inbox.  
**Fields:** `name, email, phone, subject, message, is_read`.

**Admin:** Table Inbox listing with `is_read` toggle, read/unread badge, created. Click row → View message → Mark read. Bulk delete.

---

### E5. Subscribers — `/admin/subscribers`

**Purpose:** `subscribers` from `POST /api/v1/subscribe` (Footer Newsletter) via `subscribe()` with unique email.  
**Fields:** `email` unique, `is_verified` bool (set after email verification if you add).

**Admin:** Table Newsletter list email + verified + created. Export.

**Check via API:** `GET /api/v1/subscribe/check?email=test@test.com` → `is_subscribed` bool.

---

## STEP F: Verification & MySQL Sync

### F1. Verify via APIs (no UI)
Run in terminal or curl / browser:

```bash
# Health
curl http://localhost:8000/api/health
curl http://localhost:8000/api/v1/packages?per_page=1
curl http://localhost:8000/api/v1/homepage
curl http://localhost:8000/api/v1/company
curl http://localhost:8000/api/v1/navigation
curl http://localhost:8000/api/v1/search?q=everest&type=packages
curl http://localhost:8000/api/v1/categories
curl http://localhost:8000/api/v1/destinations
curl http://localhost:8000/api/v1/regions
curl http://localhost:8000/api/v1/blogs?per_page=1
curl http://localhost:8000/api/v1/sliders
curl http://localhost:8000/api/v1/testimonials?featured=1
curl http://localhost:8000/api/v1/team
curl http://localhost:8000/api/v1/faqs
curl http://localhost:8000/api/health
```

Expect `success: true`.

### F2. Verify Admin Panel
- Visit `/admin/packages` → should list seeded 6 packages (EBC 14d, Poon Hill 5d etc) if you ran seed.
- Visit `/admin/company-settings` → change something → Save → Revisit `/api/v1/company` → changes reflected.

### F3. Sync MySQL File for Server (Critical as you requested "update mysql also when even update happen")
You have **no MySQL locally** but MySQL on server. Use:

```bash
cd backend
php artisan app:dump-mysql          # Regenerates backend/database/tour_and_travel_mysql.sql (101KB) + root copy + sqlite sql
# Or Windows double-click:
backend/sync-mysql.bat

# Then
git add backend/database/*.sql ./*.sql backend/database/migrations/*
git commit -m "update packages, sync mysql"
git push

# On server (SSH):
git pull
mysql -u root -p tour_and_travel < backend/database/tour_and_travel_mysql.sql
# or phpMyAdmin -> Import -> tour_and_travel_mysql.sql
php artisan config:clear
curl https://yourdomain.com/api/v1/packages?per_page=1
```

**Command details:** `backend/app/Console/Commands/DumpMysql.php:1` — uses PDO `sqlite:database.sqlite`, converts `AUTOINCREMENT → AUTO_INCREMENT`, adds `ENGINE=InnoDB`, dumps INSERTs with `SET FOREIGN_KEY_CHECKS=0`. Options: `--fresh` to migrate:fresh --seed before dump.

---

## STEP G: API Reference (Complete for Frontend)

> **Base:** `VITE_API_URL` env || `http://localhost:8000/api/v1`  
> **Health:** `GET /api/health` (outside v1)  
> All responses: `{ success: boolean, message?: string, data: any, meta?: pagination }`. Errors: `{ success:false, message, errors }` with HTTP 404/422.

### Table of All Endpoints

| Method | Endpoint | Controller::Method | Description | Frontend Call |
|--------|----------|--------------------|-------------|---------------|
| GET | `/health` | closure | Health check version | `fetch('/api/health')` |
| GET | `/v1/packages` | `PackageController@index` | List with filters `category,region,destination,difficulty,price_min,price_max,featured,tag,search,sort,per_page` | `fetchPackages({category:'trekking'})` |
| GET | `/v1/packages/featured` | `PackageController@featured` | featured/trending/popular 6 each | `fetchFeaturedPackages()` |
| GET | `/v1/packages/{slug}` | `PackageController@show` | Detail + related + increment views | `fetchPackageBySlug(slug)` |
| GET | `/v1/packages/{slug}/availability` | `PackageController@availability` | Check date/pax seats & pricing tier | `fetchPackageAvailability(slug,{travel_date,pax})` |
| GET | `/v1/packages/{slug}/addons` | `AddonController@forPackage` | Addons for package | `fetchPackageAddons(slug)` |
| GET | `/v1/destinations` | `DestinationController@index` | List with region | `fetchDestinations()` |
| GET | `/v1/destinations/featured` | `DestinationController@featured` | Featured limit | `fetchFeaturedDestinations()` |
| GET | `/v1/destinations/{slug}` | `DestinationController@show` | Detail + 6 packages | `fetchDestinationBySlug(slug)` |
| GET | `/v1/regions` | `RegionController@index` | List `with_destinations,featured` | `fetchRegions({with_destinations:1})` |
| GET | `/v1/regions/{slug}` | `RegionController@show` | Detail + packages paginated | `fetchRegionBySlug(slug)` |
| GET | `/v1/categories` | `CategoryController@index` | `with_packages` | `fetchCategories()` |
| GET | `/v1/categories/{slug}` | `CategoryController@show` | Category + packages | `fetchCategoryBySlug(slug)` |
| GET | `/v1/activities` | `ActivityController@index` | All active | `fetchActivities()` |
| GET | `/v1/activities/{slug}` | `ActivityController@show` | Detail + packages | `fetchActivityBySlug(slug)` |
| GET | `/v1/tags` | `TagController@index` | Product tags | `fetchTags()` |
| GET | `/v1/tags/{slug}` | `TagController@show` | Tag + packages | `fetchTagBySlug(slug)` |
| GET | `/v1/addons` | `AddonController@index` | `package_id` or `package_slug` | `fetchAddons({package_slug})` |
| GET | `/v1/addons/{slug}` | `AddonController@show` | Detail | `fetchAddonBySlug(slug)` |
| GET | `/v1/blogs` | `BlogController@index` | `category,search,per_page` | `fetchBlogs()` |
| GET | `/v1/blogs/featured` | `BlogController@featured` | Featured limit | `fetchFeaturedBlogs()` |
| GET | `/v1/blogs/{slug}` | `BlogController@show` | Detail + related + view++ | `fetchBlogBySlug(slug)` |
| GET | `/v1/blog-categories` | `BlogController@categories` | With post count | `fetchBlogCategories()` |
| GET | `/v1/blog-tags` | `BlogController@tags` | Listing | `fetchBlogTags()` |
| GET | `/v1/pages` | `PageController@index` | List published | `fetchPages()` |
| GET | `/v1/pages/{slug}` | `PageController@show` | About etc | `fetchPageBySlug(slug)` |
| GET | `/v1/homepage` | `PageController@homepage` | Aggregated sliders,testimonials,team,partners,faqs,why,settings | `fetchHomepage()` |
| GET | `/v1/settings` | `SiteController@settings` | `?group=company` non-encrypted | `fetchSettings({group:'company'})` |
| GET | `/v1/company` | `SiteController@company` | Company + SEO map | `fetchCompany()` |
| GET | `/v1/navigation` | `SiteController@navigation` | Categories, regions, destinations, pages for mega menu | `fetchNavigation()` |
| GET | `/v1/stats` | `SiteController@stats` | Counts | `fetchStats()` |
| GET | `/v1/sliders` | `SiteController@sliders` | Active ordered | `fetchSliders()` |
| GET | `/v1/testimonials` | `SiteController@testimonials` | `featured,package_id,per_page` | `fetchTestimonials({featured:1})` |
| POST | `/v1/testimonials` | `SiteController@storeTestimonial` | Guest review pending moderation | `submitTestimonial({customer_name,rating,comment})` |
| GET | `/v1/team` | `SiteController@team` | Active sorted | `fetchTeam()` |
| GET | `/v1/partners` | `SiteController@partners` | Active sorted | `fetchPartners()` |
| GET | `/v1/faqs` | `SiteController@faqs` | `?category=` | `fetchFaqs()` |
| GET | `/v1/why-choose-us` | `SiteController@whyChooseUs` | Active | `fetchWhyChooseUs()` |
| GET | `/v1/search` | `SearchController@index` | `?q=&type=all|packages|destinations|blogs` | `searchAll({q:'everest'})` |
| GET | `/v1/search/suggest` | `SearchController@suggest` | `?q=&limit=5` autocomplete | `searchSuggest({q:'ev'})` |
| POST | `/v1/coupons/validate` | `CouponController@validate` | `{code,package_id?,amount?}` returns discount/final | `validateCoupon({code:'NEPAL10',amount:1350})` |
| GET | `/v1/coupons/{code}` | `CouponController@show` | Public coupon info | `fetchCoupon(code)` |
| GET | `/v1/payments/methods` | `PaymentController@methods` | Available gateways | `fetchPaymentMethods()` |
| POST | `/v1/payments/initiate` | `PaymentController@initiate` | `{booking_code,gateway,amount?}` returns gateway_data | `initiatePayment({booking_code,gateway:'esewa'})` |
| POST | `/v1/payments/verify` | `PaymentController@verify` | `{booking_code,gateway,transaction_id,status}` | `verifyPayment({...})` |
| GET | `/v1/payments/booking/{code}` | `PaymentController@bookingPayments` | History | `fetchBookingPayments(code)` |
| GET | `/v1/payments/callback/esewa/{status}` | `PaymentController@esewaCallback` | Redirect after eSewa | (gateway redirect) |
| POST | `/v1/inquiries` | `InquiryController@store` | Package inquiry | `sendInquiry({package_id,name,email,phone,message})` |
| POST | `/v1/custom-trips` | `InquiryController@customTrip` | Custom trip form | `sendCustomTrip({...})` |
| POST | `/v1/bookings` | `BookingController@store` | Create booking | `createBooking({package_id,travel_date,pax_adult,customer_*})` |
| GET | `/v1/bookings/{code}` | `BookingController@show` | Track booking | `fetchBookingByCode(code)` |
| POST | `/v1/contact` | `ContactController@contact` | Contact message | `sendContact({name,email,phone,subject,message})` |
| POST | `/v1/subscribe` | `ContactController@subscribe` | Newsletter | `subscribe({email})` |
| GET | `/v1/subscribe/check` | `ContactController@check` | `?email=` | `checkSubscribe(email)` |

**Frontend Service:** All helpers centralized in `frontend/src/services/api.js` — import named helpers plus default `api` Axios instance.

**Example Helpers Usage in React:**
```jsx
import { fetchPackages, fetchPackageBySlug, fetchCompany, searchAll, validateCoupon, initiatePayment } from '@/services/api';

// In useEffect
const { data } = await fetchPackages({ category:'trekking', featured:1, per_page:6 });
const company = await fetchCompany(); // Header dynamic
const results = await searchAll({ q:'everest', type:'packages' });
const coupon = await validateCoupon({ code:'NEPAL10', amount:1350 });
const pay = await initiatePayment({ booking_code: 'NPL-2026-XXXX', gateway:'esewa' });
// Redirect via pay.data.data.gateway_data.esewa_form_data
```

---

## Tips & Troubleshooting

### General Tips
- **Sort Order:** Lower number = shown first. Use 0,10,20... Leave gaps to insert later.
- **Active / Published toggles:** Inactive/draft items are hidden from ALL `GET /api/v1/*` via `scopeActive`/`scopePublished`. If frontend missing items → check toggle.
- **Slugs:** Unique, lowercase hyphen. If duplicate error on save → add `-2` suffix.
- **Images:** Use compressed JPG/PNG ≤1MB, dimensions noted per section (hero 1920×800, card 800×600, team 400×400). FileUpload stores under `storage/app/public/<directory>` and needs `php artisan storage:link` (creates symlink `public/storage`). If images 404 → run `php artisan storage:link`.
- **Rich Text:** Overview/Content use `RichEditor` → outputs HTML. Frontend renders via `dangerouslySetInnerHTML`. Keep consistent heading styles (H2, H3).
- **Translations:** Not yet implemented; keep English.

### Common Issues
- **Login fails:** `admin@nepalyatra.com` not found → `php artisan db:seed --class=NepalDemoSeeder` or `php artisan make:filament-user`.
- **Images not showing in API:** `GET /api/v1/packages/{slug}` returns `featured_image` path but not URL → Ensure `storage:link` and `APP_URL` correct in `.env`. Spatie media needs `MEDIA_LIBRARY` config.
- **Package not on frontend:** Check `status=published` — `draft` hides. Also `php artisan serve` running + CORS? Check `config/cors.php` allows `localhost:5173`.
- **Payment methods disabled:** `GET /api/v1/payments/methods` shows `enabled:false` → Fill Company Settings → Tokens tab with real keys, Save.
- **MySQL import error:** `FOREIGN KEY` fails → Ensure `SET FOREIGN_KEY_CHECKS=0` in sql file (is). Import fresh DB: `CREATE DATABASE tour_and_travel CHARACTER SET utf8mb4` then import.
- **Route not found (404):** `php artisan route:list --path=api` should show 50+ routes. If missing new ones → `php artisan route:clear`, `php artisan config:clear`, `php artisan optimize:clear`.
- **Frontend 404 on `/api/v1/company`:** Ensure backend running port 8000 and `frontend/.env` `VITE_API_URL=http://localhost:8000/api/v1` → restart Vite `npm run dev`.
- **Search returns empty:** Min 2 chars for search, 1 for suggest. Try `?q=everest`.

### Performance & Security
- **Encrypted Tokens:** Never expose via API. `SiteController::settings` filters `is_encrypted=false`. If you need SMTP on frontend (no!), never log.
- **Pagination:** Default `per_page` 12 max 50 for packages; 9 for blogs; 12 for testimonials. Use `?per_page=` to adapt infinite scroll.
- **Caching:** In production add `Cache::remember` to `PackageController@index` for 5 minutes; clear on package save via Filament observer.
- **Backups:** Regularly `php artisan app:dump-mysql` + git push; also `php artisan backup:run` if you add spatie/laravel-backup.

---

## Appendix: File Map

| Purpose | File Path |
|---------|-----------|
| Master Company Page | `backend/app/Filament/Pages/ManageCompanySettings.php:1` |
| Master Package Form | `backend/app/Filament/Resources/Packages/Schemas/PackageForm.php:1` |
| Master Blog Form | `backend/app/Filament/Resources/BlogPosts/Schemas/BlogPostForm.php:1` |
| All Filament Forms (22) | `backend/app/Filament/Resources/*/Schemas/*Form.php` |
| API Routes (Complete) | `backend/routes/api.php:1` |
| All API Controllers (13) | `backend/app/Http/Controllers/Api/*Controller.php` |
| Frontend API Helpers | `frontend/src/services/api.js:1` |
| Company Config | `backend/config/company.php:1` |
| SQL Sync Command | `backend/app/Console/Commands/DumpMysql.php:1` |
| Demo Seeder | `backend/database/seeders/NepalDemoSeeder.php:1` |
| MySQL Dump | `backend/database/tour_and_travel_mysql.sql:1` |
| This Guide | `ADMIN_PANEL_GUIDE.md:1` and `docs/ADMIN_PANEL_GUIDE.md` |

---

**Generated:** 2026-08-23 | **Stack:** Laravel 11 + Filament 5.7 + SQLite/MySQL + Spatie Media/Permission + React 18 + Vite 5  
**Next Steps for you:** 
1. Login → Complete Company Settings → Follow Order 2-12 → Save each.  
2. Run `php artisan app:dump-mysql` → Import to server.  
3. Frontend team: `cd frontend && npm install && npm run dev` → Integrate pages via `src/services/api.js` helpers.  
4. Expand frontend `src/pages` beyond placeholders using this guide's API table.

*Need help? Report at https://github.com/anomalyco/opencode or ask Muse Spark for "how to add field to PackageForm and migrate".*

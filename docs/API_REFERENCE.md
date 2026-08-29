# Nepal Yatra — Complete API Reference (Frontend)

> **Base URL:** `http://localhost:8000/api/v1` (local) → set in `frontend/.env` as `VITE_API_URL`  
> **Health:** `GET http://localhost:8000/api/health`  
> **Auth:** Public API, no auth required for listed routes.  
> **Response Format:** `{ success: true, message?: string, data: mixed }` paginated lists: `{ success:true, data: { data:[], current_page, last_page, total } }`  
> **Source Files:** `backend/routes/api.php` + `backend/app/Http/Controllers/Api/*Controller.php` (13 controllers)  
> **Frontend Helpers:** `frontend/src/services/api.js` (all named exports)

## Quick Test (cURL)

```bash
curl http://localhost:8000/api/health
curl "http://localhost:8000/api/v1/packages?per_page=2"
curl "http://localhost:8000/api/v1/packages/everest-base-camp-trek-14-days"
curl "http://localhost:8000/api/v1/company"
curl "http://localhost:8000/api/v1/search?q=everest&type=packages"
```

## 1. Packages — Tour Products

| Verb | Endpoint | Params | Desc |
|------|----------|--------|------|
| GET | `/packages` | `category=trekking`, `region=everest-region`, `destination=poon-hill`, `difficulty=easy`, `price_min=500`, `price_max=2000`, `featured=1`, `tag=adventure`, `search=EBC`, `sort=price_asc\|price_desc\|duration_asc`, `per_page=12` | Paginated published with `category,destination,region,activities,tags,pricings` |
| GET | `/packages/featured` | — | `{featured, trending, popular}` 6 each |
| GET | `/packages/{slug}` | — | Full relations + `related` 4 + view++ |
| GET | `/packages/{slug}/availability` | `travel_date=2026-10-01`, `pax=2` | Group check + pricing tier + departure seats |
| GET | `/packages/{slug}/addons` | — | Addons for package |

**Model:** `Package` (published scope). Pricings filtered `is_active`.

## 2. Regions & Destinations

| GET | `/destinations` | `?featured=1&region=everest-region` | List active ordered `sort_order` |
| GET | `/destinations/featured` | `?limit=6` | Featured |
| GET | `/destinations/{slug}` | — | `region` + 6 `packages` |
| GET | `/regions` | `?with_destinations=1&featured=1&parent_id=1` | `withCount destinations,packages` + children if with_destinations |
| GET | `/regions/{slug}` | `?per_page=12` | Region + destinations + packages paginated |

## 3. Categories / Activities / Tags / Addons

| GET | `/categories` | `?with_packages=1` | `packages_count` |
| GET | `/categories/{slug}` | `?per_page=12` | Category + packages |
| GET | `/activities` | — | `packages_count` |
| GET | `/activities/{slug}` | — | Activity + packages |
| GET | `/tags` | — | Product tags `packages_count` |
| GET | `/tags/{slug}` | — | Tag + packages |
| GET | `/addons` | `?package_id=1` or `?package_slug=` | Filtered |
| GET | `/addons/{slug}` | — | Detail + 5 packages |

## 4. Blogs

| GET | `/blogs` | `?category=trekking-tips&search=&featured=1&tag=everest&per_page=9` | Paginated `category,author,tags` |
| GET | `/blogs/featured` | `?limit=6` | Featured |
| GET | `/blogs/{slug}` | — | Post + related 3 + view++ |
| GET | `/blog-categories` | — | With `posts_count` |
| GET | `/blog-tags` | — | With `posts_count` |

## 5. Pages & Homepage & Settings

| GET | `/pages` | — | List published `id,title,slug` |
| GET | `/pages/{slug}` | — | CMS page by slug (About, Terms) |
| GET | `/homepage` | — | Aggregated `sliders,testimonials,team,partners,faqs,why,settings` |
| GET | `/site/homepage` | — | Alias |
| GET | `/settings` | `?group=company` | Non-encrypted `key=>value` map |
| GET | `/company` | — | `{company, seo}` maps |
| GET | `/navigation` | — | `{categories, regions, destinations, pages}` mega menu |
| GET | `/stats` | — | `{total_packages,total_destinations,total_bookings,total_testimonials}` |

## 6. Sliders / Testimonials / Team / Partners / FAQ / Why

| GET | `/sliders` | — | Active ordered |
| GET | `/testimonials` | `?featured=1&package_id=1&per_page=12` | Paginated approved |
| POST | `/testimonials` | `{package_id?,customer_name,customer_country?,rating 1-5,comment,trip_date?,avatar?}` | Creates `pending` 201 |
| GET | `/team` | — | Active sorted |
| GET | `/partners` | — | Active sorted |
| GET | `/faqs` | `?category=booking` | Active ordered |
| GET | `/why-choose-us` | — | Active |

## 7. Search

| GET | `/search` | `?q=everest&type=all\|packages\|destinations\|regions\|blogs&per_page=10` | Validated min 2 |
| GET | `/search/suggest` | `?q=ev&limit=5` | Lightweight autocomplete |

## 8. Coupons

| POST | `/coupons/validate` | `{code, package_id?, amount?}` | Returns `{coupon,discount,final_amount}` or 422 reason expired/limit/restricted |
| GET | `/coupons/{code}` | — | Public `code,discount_type,value,is_valid,valid_from,to` |

## 9. Payments

| GET | `/payments/methods` | — | `[{key,name,currency,enabled}]` check vs tokens |
| POST | `/payments/initiate` | `{booking_code,gateway: esewa\|khalti\|stripe\|bank, amount?}` | Creates `pending` Payment + `gateway_data` (esewa_form_data, khalti amount×100, stripe client_secret, bank details) |
| POST | `/payments/verify` | `{booking_code,gateway,transaction_id?,status?,raw_response?}` | Verify & update `payment_status=paid`, `booking_status=confirmed` if completed |
| GET | `/payments/booking/{code}` | — | History + total_paid + balance |
| GET | `/payments/callback/esewa/{status}` | `?oid=&amt=&refId=` | Browser redirect after eSewa; JSON shows next verify step |

**Tokens:** `Setting::get('tokens.esewa_merchant_code')` etc.

## 10. Inquiry / Custom Trip / Booking / Contact / Subscribe

| POST | `/inquiries` | `{package_id?,name,email,phone,country?,travel_date?,pax?,message}` | 201 inquiry + TODO mail |
| POST | `/custom-trips` | `{name,email,phone,country?,destination_interest?,duration_days?,budget?,travel_date?,pax?,interests?,message?}` | 201 |
| POST | `/bookings` | `{package_id,departure_id?,travel_date,pax_adult,pax_child?,customer_name,customer_email,customer_phone,customer_country?,special_request?,travelers?:[{full_name,passport_no?,nationality,dob?,gender?}]}` | Transactional create booking + travelers + inc seats_booked. Calc `totalAmount = package.finalPrice()*totalPax`. 201 returns `booking_code` |
| GET | `/bookings/{code}` | — | `package,departure,travelers,payments` |
| POST | `/contact` | `{name,email,phone?,subject?,message}` | 201 ContactMessage |
| POST | `/subscribe` | `{email unique}` | 201 Subscriber |
| GET | `/subscribe/check` | `?email=` | `{is_subscribed}` |

## Response Shape (example)

`GET /api/v1/packages?category=trekking&per_page=2`:
```json
{
  "success": true,
  "message": "Packages fetched successfully",
  "data": {
    "current_page": 1,
    "data": [
      {"id":1,"title":"Everest Base Camp","slug":"everest-base-camp","price":"1350.00","discount_price":"1200.00","category":{"name":"Trekking"},"destination":{"name":"EBC"},"tags":[{"name":"Adventure"}],"pricings":[]},
      {"id":2,"title":"Poon Hill","slug":"poon-hill","price":"600.00","category":{"name":"Trekking"}}
    ],
    "total": 6,
    "per_page": 2
  }
}
```

## Errors

- 404 `{success:false,message:"Package not found"}`
- 422 validation `{success:false,message:"The given data was invalid.",errors:{field:[]}}` Laravel default
- 422 coupon `{success:false,message:"Coupon has expired"}`

## Frontend Usage (api.js)

```js
import { fetchPackages, fetchPackageBySlug, fetchCompany, searchAll, validateCoupon, initiatePayment, createBooking } from './services/api';

// Listing
const res = await fetchPackages({ category:'trekking', featured:1, per_page:6 });
const packages = res.data.data.data || res.data.data;

// Detail
const { data } = await fetchPackageBySlug('everest-base-camp');
const pkg = data.data.package;

// Company header
const company = (await fetchCompany()).data.data.company; // { 'company.name': '...', 'company.phone': '...' }

// Search
const results = await searchAll({ q:'everest', type:'packages' });

// Booking
const booking = await createBooking({ package_id:1, travel_date:'2026-10-01', pax_adult:2, customer_name:'John', customer_email:'j@j.com', customer_phone:'+977', travelers:[{full_name:'John', nationality:'US'}]});
const code = booking.data.data.booking_code; // NPL-2026-XXXX
await initiatePayment({ booking_code: code, gateway:'esewa' });
```

## Version & Auth

- Version 1.0.0 (`/api/health` → version)
- No auth for public; add `Auth` middleware later for user bookings if you implement login. For now all open.

## Need Non-List Detail?

All list endpoints also have `?with_*` expands: e.g., categories `with_packages`, regions `with_destinations`. Use for homepage sections without extra calls.

— Generated 2026-08-23 from `routes/api.php` vCOMPLETE (13 controllers, 40+ endpoints).

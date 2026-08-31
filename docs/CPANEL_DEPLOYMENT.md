# cPanel Deployment Guide (403 Forbidden Fix + Super Admin + Profile)

> **Language:** Nepali + English mix (as requested)
> **Problem:** `composer install`, `.env` setup, `mysql db` setup, `user banaye` but `403 Forbidden` because permission not assigned & document root wrong.
> **Fix includes:** Root `.htaccess`, Shield bypass, `AdminSeeder` only, profile via user icon.

---

## 1. Yesma k bhayo? (Root Cause)

1. **Document root galat:** cPanel ma default `public_html` is document root. Laravel ko document root `public` ho. Timile pura `backend` folder `public_html` ma copy garepaxi, browser `public_html/index.php` khojxa but Laravel ko `index.php` is `public_html/public/index.php` ma xa → direct 403 / 404.
2. **Shield permission 403:** `spatie/laravel-permission` + `filament-shield` ma `super_admin` le matra sabai access pauxa. Normal user banaye but `role` assign nagare ya `Permission` generate nagare → `Gate::before` le allow gardaina → Policies (`UserPolicy:viewAny` etc) le `can('ViewAny:User')` fail → 403.
3. **Missing storage link / permissions:** `storage/` + `bootstrap/cache/` writable xaina → 500/403.

### Ke gara fix?

- `backend/.htaccess` (root htaccess) added → `public/` ma rewrite (case 1 & 2 both works)
- `app/Providers/AppServiceProvider.php: Gate::before` super_admin bypass (role column + spatie role both check)
- `database/seeders/AdminSeeder.php` ONLY super admin `admin@maptechnepal.com / admin123`
- `app/Filament/Pages/Auth/EditProfile.php` + `AdminPanelProvider->profile()` → user icon ma **My Profile**

---

## 2. cPanel ma file halne correct tarika (2 options)

### Option A (Recommended - Most correct)
cPanel → **Domains → Document Root** set to `public_html/backend/public` or `public_html/public`

```
File Manager:
  public_html/
    backend/          # pura laravel project
      public/         # document root point here
      app/
      .env
      ...
```
cPanel Domain → Document Root = `/home/youruser/public_html/backend/public`  
Then NO need root `.htaccess` rewrite (but we added safe rewrite, still works).

### Option B (If cannot change document root - typical shared hosting)
Copy pura `backend` content to `public_html`:

```
public_html/
  app/
  bootstrap/
  config/
  database/
  public/         # laravel public folder still inside
  storage/
  vendor/
  .env
  .htaccess       # <-- OUR new root htaccess (rewrites to public/)
  public/.htaccess # <-- existing laravel public htaccess
```

We added `backend/.htaccess` which does:
```
RewriteRule ^(.*)$ public/$1 [L]
```
So `https://yourdomain.com/admin` → internally `public/index.php` → works without changing document root.

**Check:** `https://yourdomain.com/health` → `https://yourdomain.com/public/health` internal.

---

## 3. Super Admin Seeder (Ke matra data halne)

**Requirement:** `aaru data kei halnu pardaina jus super admin banaune file matra hal seeder ma. admin email admin@maptechnepal.com password admin123`

- `database/seeders/AdminSeeder.php` created → ONLY super admin
- `database/seeders/DatabaseSeeder.php` updated → calls ONLY `AdminSeeder`, NOT `NepalDemoSeeder`
- Demo data chahiyema manually: `php artisan db:seed --class=NepalDemoSeeder`

**Admin credentials:**

| Field | Value |
|-------|-------|
| Email | `admin@maptechnepal.com` |
| Password | `admin123` |
| Role | `super_admin` |
| Login URL | `https://yourdomain.com/admin` |

**Seeder logic:**
1. `Role::firstOrCreate(super_admin, admin, editor, agent, panel_user)`
2. `Artisan::call('shield:generate --all')` if no permissions (378 permissions)
3. `User::firstOrCreate(['email'=>admin@maptechnepal.com], ... Hash::make('admin123'))` → always resets password to `admin123` (idempotent)
4. `assignRole('super_admin')`
5. `syncPermissions(Permission::all())` + clear cache

---

## 4. Profile Feature (User icon → My Profile)

**Requirement:** `user le aafno profile aafai update garcha so user ko icon ma profile update garne feature rakh`

- `app/Filament/Pages/Auth/EditProfile.php` custom page → avatar upload, name, email, phone, password change (with current password verification)
- `app/Providers/Filament/AdminPanelProvider.php` → `.profile(EditProfile::class, isSimple:false)` + `.passwordReset()` + `userMenuItems([ profile => My Profile ])`
- `app/Models/User.php` → `getFilamentAvatarUrl()` + `canAccessPanel()` (inactive block)

**Use:**
1. Topbar right user icon click → **My Profile** → `/admin/profile`
2. Form: Avatar (avatars/), Full Name, Phone, Email, New Password + Confirm + Current Password (required for email/password change)
3. Save → stays on page, notification success.

**Filament native EditProfile already handles:** password hashing, email verification, rate limiting (5 attempts).

---

## 5. cPanel Live Deployment Step-by-Step (Copy-Paste Commands)

### 5.1 Upload
- `backend` folder zip → cPanel File Manager → Upload → Extract in `public_html` or `/home/user/tour_backend`

### 5.2 SSH / Terminal (cPanel → Terminal or SSH)
```bash
cd ~/public_html/backend   # or cd ~/tour_backend

# 1. Install
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Env
cp .env.example .env
nano .env
# Fill:
# APP_NAME="MapTech Nepal"
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://yourdomain.com
# FRONTEND_URL=https://yourdomain.com
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=your_cpanel_db
# DB_USERNAME=your_cpanel_user
# DB_PASSWORD=...

php artisan key:generate --force

# 3. Permissions (critical for 403 fix)
chmod -R 775 storage bootstrap/cache
chmod -R 755 public

# 4. Migrate + Seed ONLY super admin
php artisan migrate --force
php artisan db:seed --force
# or explicitly: php artisan db:seed --class=AdminSeeder --force

# Verify:
php artisan tinker --execute="echo \App\Models\User::where('email','admin@maptechnepal.com')->first()->email;"

# 5. Storage link (symlink may be disabled on cPanel)
php artisan storage:link
# If fails (symlink disabled): manually via File Manager:
# Create symlink: public/storage -> ../storage/app/public
# OR copy files: cp -r storage/app/public/* public/storage/
# OR create PHP file: php -r "symlink('../storage/app/public', 'public/storage');"

# 6. Shield permissions (AdminSeeder already does, but manual also):
php artisan shield:generate --all

# 7. Cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Test
curl https://yourdomain.com/api/health
curl https://yourdomain.com/api/v1/packages?per_page=1
# Should return JSON not 403

# Login:
# https://yourdomain.com/admin  → admin@maptechnepal.com / admin123
```

### 5.3 If Still 403

**Checklist:**

1. **.htaccess exists?** `ls -la .htaccess public/.htaccess` → both should exist (root + public)
2. **Document root correct?** cPanel → Domains → Document Root should be `.../public` if Option A, else rely on root htaccess.
3. **Permission cache?** `php artisan permission:cache-reset` + `php artisan cache:clear`
4. **User has super_admin?**
   ```bash
   php artisan tinker
   >>> \App\Models\User::where('email','admin@maptechnepal.com')->first()->hasRole('super_admin')
   => true
   >>> \Spatie\Permission\Models\Permission::count()
   => 378
   ```
   If 0 permissions → `php artisan shield:generate --all` then reseed.
5. **Storage permissions:** `ls -ld storage` → `drwxrwxr-x` 775, try `chmod -R 777 storage` temporary test (then back to 775).
6. **APP_KEY set?** `grep APP_KEY .env` → not empty.
7. **Check logs:** `tail -f storage/logs/laravel.log`

### 5.4 Common cPanel MySQL Setup
cPanel → **MySQL Databases → Create DB + User → Add User to DB (All Privileges)**  
Then fill `.env` as above.

---

## 6. Local Verification (Already tested)

```bash
php artisan migrate --force  # adds customer enum fix
php artisan db:seed --class=AdminSeeder
# Output:
# Seeding Super Admin (admin@maptechnepal.com)...
# Synced 378 permissions to super_admin role
# ✓ Super Admin ready: admin@maptechnepal.com / admin123

php artisan route:list | findstr profile
# → admin/profile → App\Filament\Pages\Auth\EditProfile

# Gate bypass test:
# isSuperAdmin: true
# Gate ViewAny:User: true
```

---

## 7. Files Changed (For user reference)

| File | Change | Why |
|------|--------|-----|
| `app/Providers/AppServiceProvider.php:14-31` | `Gate::before` super_admin bypass | Fix 403 even if permissions not generated |
| `app/Models/User.php:129-162` | `getFilamentAvatarUrl()`, `canAccessPanel()`, fillable `customer` handling | Profile avatar + panel access for active users |
| `app/Providers/Filament/AdminPanelProvider.php:39-45` | `->profile(EditProfile::class)` + `passwordReset()` + `userMenuItems` | User icon → My Profile |
| `app/Filament/Pages/Auth/EditProfile.php` | NEW | Custom profile with avatar, phone, password |
| `database/seeders/AdminSeeder.php` | NEW | ONLY super admin admin@maptechnepal.com / admin123 + 378 perms |
| `database/seeders/DatabaseSeeder.php` | Only call AdminSeeder | No demo data as requested |
| `database/migrations/2026_08_31_000001_add_customer_to_users_role_enum.php` | NEW | Fix enum for customer role (SQLite/MySQL safe) |
| `database/migrations/2026_08_22_070018_create_system_tables.php:57` | Add `customer` to enum | Allow customer role insert |
| `.htaccess` (root) | NEW | cPanel rewrite to public/ if doc root not changed |
| `public/.htaccess` | unchanged (existing) | Already correct |
| `docs/CPANEL_DEPLOYMENT.md` | NEW | This guide |

---

## 8. Security Notes (Production)

- Change password after first login: My Profile → New Password
- Set `APP_DEBUG=false` in `.env` (never expose stack trace)
- Set `PAYMENT_MOCK_ENABLED=false` and fill real gateway tokens in **Admin → Company Settings → Tokens**
- `.htaccess` already denies `.env`, `.git`, `composer.json` direct access
- `Gate::before` only for super_admin, other roles still need explicit permissions via `/admin/shield/roles`

---

## 9. Quick Test After Live

1. Visit `https://yourdomain.com/admin` → login `admin@maptechnepal.com` / `admin123` → **should NOT 403, should show Dashboard**
2. Click top-right user avatar → **My Profile** → change name/phone/avatar → Save → should success
3. Visit `https://yourdomain.com/api/v1/homepage` → JSON success
4. Create test package (Tour Management → Packages → New) with `published` → then `curl https://yourdomain.com/api/v1/packages/{slug}` → shows.

If any 403 still, check `storage/logs/laravel.log` and run `php artisan optimize:clear`.


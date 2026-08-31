<?php
/**
 * File: app/Models/User.php
 * Purpose: Represents system users - Admins, Editors, Agents for Filament panel,
 *          and also customers if they register via API.
 *          Used by: Auth, Filament Shield for RBAC, Blog posts (author), Bookings, Inquiries.
 *          Table: users (migration: 0001_01_01_000000_create_users_table + 2026_08_22_070018)
 */

namespace App\Models;

// Use Illuminate Contracts if email verification needed
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Trait: Enables Shield RBAC - assign roles/permissions
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * Represents a user in the system.
 * Attributes: name, email, password, phone, avatar, role, is_active
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     * These can be filled via User::create([...])
     * @var list<string>
     */
    protected $fillable = [
        'name', // User full name
        'email', // Unique email for login
        'password', // Hashed password
        'phone', // Phone/whatsapp
        'avatar', // Avatar image path
        'role', // Enum role: super_admin, admin, editor, agent
        'is_active', // Boolean active flag
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Hidden when converting to JSON (API responses)
     * @var list<string>
     */
    protected $hidden = [
        'password', // Never expose password hash
        'remember_token', // Hide remember token
    ];

    /**
     * Get the attributes that should be cast.
     * Casts handle type conversion automatically.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Cast to Carbon datetime
            'password' => 'hashed', // Auto hash when setting password
            'is_active' => 'boolean', // Cast to boolean
        ];
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get all bookings created by this user (if customer) or assigned.
     * Relation: User 1--N Booking
     * @return HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class); // Foreign key user_id in bookings table
    }

    /**
     * Get blog posts authored by this user.
     * @return HasMany
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'user_id'); // Author relation
    }

    /**
     * Get inquiries assigned to this user (staff).
     * @return HasMany
     */
    public function assignedInquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'assigned_to');
    }

    // ==================== SCOPES & HELPERS ====================

    /**
     * Scope: Only active users.
     * Usage: User::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if user is super admin (has all permissions).
     * Used in: Filament Shield checks, AdminPanelProvider
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->hasRole('super_admin');
    }

    /**
     * Get display name for Filament.
     * Filament uses this for topbar user menu.
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }

    /**
     * Get avatar URL for Filament topbar (user icon).
     * Filament checks this method to show user avatar in header.
     * Return storage URL if avatar exists, else null (shows initials).
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar) {
            // avatar stored as 'avatars/xxx.jpg' via FileUpload directory('avatars')
            // asset('storage/...') works when `php artisan storage:link` is done (cPanel: ensure symlink or copy)
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * Determine if user can access Filament panel - blocks inactive users.
     * Filament calls this via AdminPanelProvider if defined.
     * Production fix: "ke garda ne 403 na aune" - any active user can access panel, Gate::before handles resource perms
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Only block inactive users - all active users can at least see dashboard (prevents login 403)
        // This ensures login pachi dashboard 403 kahile aundaina
        return (bool) $this->is_active;
    }
}

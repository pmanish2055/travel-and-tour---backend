<?php
/**
 * File: app/Models/Setting.php
 * Purpose: Key-value store for company detail, tokens, general settings.
 *          GROUPED by `group` (company, tokens, seo, general).
 *          Tokens/keys (eSewa, Khalti, Stripe, Google Maps, SMTP, etc) are stored with is_encrypted=true
 *          and use Laravel encrypted casting for security. Only Super Admin can view Tokens tab.
 *          Table: settings
 *          Used by: Filament ManageSettings Page, helpers, PaymentController, Mail config.
 *
 *          Token Keys Stored Here:
 *          - tokens.google_map_api_key (encrypted) -> for destination maps
 *          - tokens.google_analytics_id (plain) -> layouts head
 *          - tokens.smtp_host, smtp_port, smtp_user, smtp_pass (encrypted) -> mail
 *          - tokens.esewa_merchant_code, esewa_secret (encrypted) -> Payment verify
 *          - tokens.khalti_public_key, khalti_secret (encrypted)
 *          - tokens.stripe_publishable, stripe_secret, stripe_webhook_secret (encrypted)
 *          - tokens.recaptcha_site_key, recaptcha_secret (encrypted)
 *          - tokens.whatsapp_token, whatsapp_phone_id (encrypted)
 *          - tokens.facebook_pixel_id, etc
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    // === Fillable: allow mass assignment for these columns ===
    protected $fillable = [
        'group', // Group name: general, company, tokens, seo
        'key', // Unique key e.g., company.name or tokens.esewa_secret
        'value', // Value - stored encrypted if is_encrypted=true
        'is_encrypted', // Boolean flag
        'description', // Helper text for admin UI
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
    ];

    // ==================== ACCESSOR / MUTATOR FOR ENCRYPTION ====================
    /**
     * Accessor: Decrypt value when reading if is_encrypted is true.
     * Called automatically when $setting->value is accessed.
     * Security: Ensures tokens are stored encrypted at rest.
     */
    protected function value(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // If encrypted, try to decrypt; if fail (plain), return as is
                if ($this->is_encrypted && $value) {
                    try {
                        return Crypt::decryptString($value);
                    } catch (\Exception $e) {
                        return $value; // Fallback for plain values
                    }
                }
                return $value;
            },
            set: function ($value) {
                // If flagged encrypted, encrypt before saving
                if ($this->is_encrypted && $value) {
                    return Crypt::encryptString($value);
                }
                return $value;
            }
        );
    }

    // ==================== STATIC HELPERS ====================
    /**
     * Get setting value by key.
     * Usage: Setting::get('company.name'), Setting::get('tokens.esewa_secret')
     * @param string $key The unique key
     * @param mixed $default Default if not found
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key (creates or updates).
     * Handles encryption automatically based on is_encrypted flag.
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param bool $isEncrypted Whether to encrypt this value
     * @param string|null $description
     * @return Setting
     */
    public static function set(string $key, $value, string $group = 'general', bool $isEncrypted = false, ?string $description = null): self
    {
        if ($group === 'general' && str_contains($key, '.')) {
            $group = explode('.', $key)[0];
        }
        // Order matters: set is_encrypted BEFORE value so Attribute mutator encrypts correctly
        $model = static::firstOrNew(['key' => $key]);
        $model->group = $group;
        $model->description = $description;
        $model->is_encrypted = $isEncrypted;
        // Now set value (mutator will encrypt if is_encrypted true)
        $model->value = $value;
        $model->save();
        return $model;
    }

    /**
     * Scope: Filter by group.
     * Usage: Setting::group('tokens')->get()
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope: Only encrypted tokens.
     */
    public function scopeEncrypted($query)
    {
        return $query->where('is_encrypted', true);
    }
}

<?php
/**
 * File: app/Models/Booking.php
 * Purpose: Stores a confirmed booking for a package. Workflow pending->confirmed->completed.
 *          Has many travelers and payments. Generates booking_code.
 *          Table: bookings
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_code','user_id','package_id','departure_id','travel_date',
        'pax_adult','pax_child','total_amount','advance_amount','payment_status','booking_status',
        'special_request','source','customer_name','customer_email','customer_phone','customer_country','cancelled_at'
    ];
    protected $casts = ['travel_date'=>'date','total_amount'=>'decimal:2','advance_amount'=>'decimal:2','cancelled_at'=>'datetime'];
    protected static function booted(): void
    {
        static::creating(function ($b){
            if(empty($b->booking_code)) {
                // 10-char crypto-random (62^10 ~ 8e17 combos) vs old 6-char (~5e10) -> prevents enumeration/IDOR
                $b->booking_code = 'NPL-'.date('Y').'-'.strtoupper(Str::random(10));
            }
        });
    }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function departure(): BelongsTo { return $this->belongsTo(PackageDeparture::class, 'departure_id'); }
    public function travelers(): HasMany { return $this->hasMany(BookingTraveler::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function scopePending($q){ return $q->where('booking_status','pending'); }
    public function scopeConfirmed($q){ return $q->where('booking_status','confirmed'); }
    public function totalPax(): int { return $this->pax_adult + $this->pax_child; }
    public function balance(): float { return $this->total_amount - $this->advance_amount; }
}

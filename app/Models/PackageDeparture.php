<?php
/**
 * File: app/Models/PackageDeparture.php
 * Purpose: Fixed departure date for a package. Customers can book specific date.
 *          Tracks seats total/booked and status.
 *          Table: package_departures
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageDeparture extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','departure_date','return_date','price','seats_total','seats_booked','status','note'];
    protected $casts = ['departure_date'=>'date','return_date'=>'date','price'=>'decimal:2'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class, 'departure_id'); }
    public function isAvailable(): bool { return $this->status === 'open' && $this->seats_booked < $this->seats_total; }
    public function remainingSeats(): int { return max(0, $this->seats_total - $this->seats_booked); }
    public function scopeOpen($q) { return $q->where('status','open'); }
    public function scopeUpcoming($q) { return $q->where('departure_date','>=', now()); }
}

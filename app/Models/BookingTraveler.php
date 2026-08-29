<?php
/**
 * File: app/Models/BookingTraveler.php
 * Purpose: Individual traveler details for a booking (for TIMS/permit).
 *          Table: booking_travelers
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTraveler extends Model
{
    use HasFactory;
    protected $fillable = ['booking_id','full_name','passport_no','nationality','dob','gender','is_lead'];
    protected $casts = ['dob'=>'date','is_lead'=>'boolean'];
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
}

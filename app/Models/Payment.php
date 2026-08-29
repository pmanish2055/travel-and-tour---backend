<?php
/**
 * File: app/Models/Payment.php
 * Purpose: Tracks gateway payments (eSewa, Khalti, Stripe, bank) for a booking.
 *          Stores gateway, transaction_id, raw response JSON.
 *          Table: payments
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['booking_id','gateway','transaction_id','amount','currency','status','raw_response'];
    protected $casts = ['amount'=>'decimal:2','raw_response'=>'array'];
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function isCompleted(): bool { return $this->status === 'completed'; }
}

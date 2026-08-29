<?php
/**
 * File: app/Models/Inquiry.php
 * Purpose: Package inquiry/lead. Simpler than booking - captures interest.
 *          Can be converted to booking. Assigned to staff.
 *          Table: inquiries
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','name','email','phone','country','travel_date','pax','message','status','assigned_to'];
    protected $casts = ['travel_date'=>'date','pax'=>'integer'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function scopeNew($q){ return $q->where('status','new'); }
}

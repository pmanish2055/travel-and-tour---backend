<?php
/**
 * File: app/Models/CustomTrip.php
 * Purpose: Build-your-own-trip request (custom itinerary).
 *          Table: custom_trips
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomTrip extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','phone','country','destination_interest','duration_days','budget','travel_date','pax','interests','message','status'];
    protected $casts = ['travel_date'=>'date','budget'=>'decimal:2'];
    public function scopeNew($q){ return $q->where('status','new'); }
}

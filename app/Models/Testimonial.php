<?php
/**
 * File: app/Models/Testimonial.php
 * Purpose: Customer testimonial/review. Can be linked to package or general.
 *          Table: testimonials
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','customer_name','customer_country','avatar','rating','comment','trip_date','is_featured','status'];
    protected $casts = ['rating'=>'integer','is_featured'=>'boolean','trip_date'=>'date'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function scopeApproved($q){ return $q->where('status','approved'); }
    public function scopeFeatured($q){ return $q->where('is_featured', true); }
}

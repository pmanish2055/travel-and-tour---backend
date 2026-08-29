<?php
/**
 * File: app/Models/PackageItinerary.php
 * Purpose: Day-wise itinerary item for a package. Belongs to Package.
 *          Shown as timeline/tabs on frontend package detail.
 *          Table: package_itineraries
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageItinerary extends Model
{
    use HasFactory;
    protected $fillable = [
        'package_id', 'day_number', 'title', 'description',
        'max_altitude_m', 'meals', 'accommodation', 'overnight_at', 'walking_hours', 'sort_order'
    ];
    protected $casts = ['day_number'=>'integer','max_altitude_m'=>'integer'];
    /** Parent package */
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
}

<?php
/**
 * File: app/Models/PackageEquipment.php
 * Purpose: Gear/equipment list per package (Nepal specific).
 *          Table: package_equipment
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageEquipment extends Model
{
    use HasFactory;
    protected $table = 'package_equipment'; // Explicit table name
    protected $fillable = ['package_id','item','description','is_required','sort_order'];
    protected $casts = ['is_required'=>'boolean'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
}

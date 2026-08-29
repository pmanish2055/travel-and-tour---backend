<?php
/**
 * File: app/Models/PackageInclusion.php
 * Purpose: Inclusion/Exclusion item for package. Type discriminates include vs exclude.
 *          Table: package_inclusions
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageInclusion extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','type','title','description','icon','sort_order'];
    protected $casts = ['sort_order'=>'integer'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
    public function scopeIncludes($q) { return $q->where('type','include'); }
    public function scopeExcludes($q) { return $q->where('type','exclude'); }
}

<?php
/**
 * File: app/Models/PackageFaq.php
 * Purpose: FAQ per package.
 *          Table: package_faqs
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFaq extends Model
{
    use HasFactory;
    protected $fillable = ['package_id','question','answer','sort_order'];
    public function package(): BelongsTo { return $this->belongsTo(Package::class); }
}

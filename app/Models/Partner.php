<?php
/**
 * File: app/Models/Partner.php
 * Purpose: Partner/association (TAAN, NTB) logo for footer.
 *          Table: partners
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;
    protected $fillable = ['name','logo','website','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function scopeActive($q){ return $q->where('is_active', true)->orderBy('sort_order'); }
}

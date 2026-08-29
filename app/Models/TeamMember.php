<?php
/**
 * File: app/Models/TeamMember.php
 * Purpose: Team member / guide info for About page.
 *          Table: team_members
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;
    protected $fillable = ['name','designation','photo','bio','facebook','instagram','linkedin','sort_order','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function scopeActive($q){ return $q->where('is_active', true)->orderBy('sort_order'); }
}

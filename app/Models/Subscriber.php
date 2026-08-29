<?php
/**
 * File: app/Models/Subscriber.php
 * Purpose: Newsletter subscriber email.
 *          Table: subscribers
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = ['email','is_verified','verified_at'];
    protected $casts = ['is_verified'=>'boolean','verified_at'=>'datetime'];
    public function scopeVerified($q){ return $q->where('is_verified', true); }
}

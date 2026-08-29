<?php
/**
 * File: app/Models/ContactMessage.php
 * Purpose: Stores contact form submissions.
 *          Table: contact_messages
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactMessage extends Model
{
    use HasFactory;
    protected $fillable = ['name','email','phone','subject','message','is_read'];
    protected $casts = ['is_read'=>'boolean'];
    public function scopeUnread($q){ return $q->where('is_read', false); }
    public function markAsRead(): void { $this->update(['is_read' => true]); }
}

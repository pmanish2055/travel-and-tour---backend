<?php
/**
 * Migration: Create System Tables (Settings, Contacts, Subscribers)
 * Purpose: Settings stores company detail + tokens/keys (encrypted).
 *          ContactMessages from contact form, Subscribers for newsletter.
 *          Settings group: company, tokens, seo, general - used by Filament Settings Page.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Settings: key-value store for company detail & tokens
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general')->comment('Group: general, company, tokens, seo, appearance');
            $table->string('key')->unique()->comment('Unique key e.g., company.name, tokens.esewa_secret');
            $table->longText('value')->nullable()->comment('Value - JSON or plain text, encrypted if is_encrypted');
            $table->boolean('is_encrypted')->default(false)->comment('Whether value is encrypted');
            $table->string('description')->nullable()->comment('Helper text for admin - what this key does');
            $table->timestamps();
            $table->index('group');
        });
        // Contact messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Sender name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
        // Subscribers
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique()->comment('Subscriber email');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
        // Extend users table with extra fields if not exists
        // We do via separate Schema::table for safety
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email')->comment('Phone/whatsapp');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('phone')->comment('Avatar path');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['super_admin','admin','editor','agent'])->default('agent')->after('avatar')->comment('Role for Shield - super_admin has all access');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role')->comment('Active flag');
            }
        });
    }
    public function down(): void {
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('settings');
        // Do not drop users columns in down for safety
    }
};

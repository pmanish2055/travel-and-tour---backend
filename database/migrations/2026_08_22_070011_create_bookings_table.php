<?php
/**
 * Migration: Create Bookings Table
 * Purpose: Stores bookings made by tourists for a package + departure.
 *          Core sales table. Links to user (nullable for guest), package, departure.
 *          Workflow: pending -> confirmed -> completed / cancelled
 *          Related: BookingTraveler (hasMany), Payment (hasMany)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique()->comment('Human readable code e.g., NPL-2026-0001');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('FK to users - nullable for guest bookings');
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->foreignId('departure_id')->nullable()->constrained('package_departures')->nullOnDelete()->comment('FK to departures - null for private bookings');
            $table->date('travel_date')->comment('Actual travel start date');
            $table->smallInteger('pax_adult')->default(1)->comment('Adult count');
            $table->smallInteger('pax_child')->default(0)->comment('Child count');
            $table->smallInteger('pax_total')->storedAs('pax_adult + pax_child')->comment('Computed total');
            $table->decimal('total_amount', 10, 2)->comment('Total booking amount');
            $table->decimal('advance_amount', 10, 2)->default(0)->comment('Advance paid');
            $table->enum('payment_status', ['unpaid','partial','paid','refunded'])->default('unpaid');
            $table->enum('booking_status', ['pending','confirmed','cancelled','completed'])->default('pending');
            $table->text('special_request')->nullable()->comment('Customer special requests');
            $table->string('source')->nullable()->comment('web/whatsapp/agent');
            $table->string('customer_name')->comment('Lead traveler name');
            $table->string('customer_email')->comment('Email for confirmations');
            $table->string('customer_phone')->comment('Phone/whatsapp');
            $table->string('customer_country')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('package_id');
            $table->index('booking_status');
            $table->index('payment_status');
            $table->index('travel_date');
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};

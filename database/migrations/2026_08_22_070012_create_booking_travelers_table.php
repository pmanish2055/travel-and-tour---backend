<?php
/**
 * Migration: Create Booking Travelers Table
 * Purpose: Stores individual traveler details for a booking (for TIMS card, permits).
 *          Required for Nepal permits - name, passport, nationality, DOB.
 *          Belongs to Booking.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete()->comment('FK to bookings');
            $table->string('full_name')->comment('As per passport');
            $table->string('passport_no')->nullable()->comment('Passport number for permits');
            $table->string('nationality')->comment('Country');
            $table->date('dob')->nullable()->comment('Date of birth');
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->boolean('is_lead')->default(false)->comment('Is lead traveler');
            $table->timestamps();
            $table->index('booking_id');
        });
    }
    public function down(): void { Schema::dropIfExists('booking_travelers'); }
};

<?php
/**
 * Migration: Create Inquiries Table
 * Purpose: Stores inquiries/leads for packages (and general). Simpler than booking,
 *          just captures interest. Used by InquiryResource. Can convert to booking.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete()->comment('FK to packages - nullable for general inquiry');
            $table->string('name')->comment('Inquirer name');
            $table->string('email')->comment('Email');
            $table->string('phone')->comment('Phone/whatsapp');
            $table->string('country')->nullable();
            $table->date('travel_date')->nullable()->comment('Planned travel date');
            $table->smallInteger('pax')->nullable()->comment('Number of travelers');
            $table->text('message')->comment('Inquiry message');
            $table->enum('status', ['new','contacted','converted','closed'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete()->comment('Staff assigned');
            $table->timestamps();
            $table->index('package_id');
            $table->index('status');
        });
        // Custom trips - build your own trip requests
        Schema::create('custom_trips', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('country')->nullable();
            $table->string('destination_interest')->nullable()->comment('Where they want to go');
            $table->smallInteger('duration_days')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->date('travel_date')->nullable();
            $table->smallInteger('pax')->nullable();
            $table->text('interests')->nullable()->comment('Interests e.g., trekking, cultural');
            $table->text('message')->nullable();
            $table->enum('status', ['new','contacted','converted','closed'])->default('new');
            $table->timestamps();
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('custom_trips'); Schema::dropIfExists('inquiries'); }
};

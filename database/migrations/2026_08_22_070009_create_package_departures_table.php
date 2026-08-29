<?php
/**
 * Migration: Create Package Departures Table
 * Purpose: Fixed departure dates for packages. For groups joining. Includes price override, availability.
 *          Used by Booking: user selects a departure or private date.
 *          Filament: DepartureRelationManager inside PackageResource
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->date('departure_date')->comment('Start date of tour');
            $table->date('return_date')->comment('End date');
            $table->decimal('price', 10, 2)->nullable()->comment('Price for this departure - overrides package price if set');
            $table->smallInteger('seats_total')->default(16)->comment('Total seats');
            $table->smallInteger('seats_booked')->default(0)->comment('Booked seats counter');
            $table->enum('status', ['open','guaranteed','closed','cancelled'])->default('open')->comment('Availability status');
            $table->string('note')->nullable()->comment('Note e.g., Festival departure');
            $table->timestamps();
            $table->index('package_id');
            $table->index('departure_date');
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('package_departures'); }
};

<?php
/**
 * Migration: Create Package Itineraries Table
 * Purpose: Day-wise itinerary for each package. E.g., Day 1: Arrival, Day 2: Trek to Namche.
 *          Belongs to Package. Used in PackageResource Repeater and frontend tabs.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->smallInteger('day_number')->comment('Day 1,2,3...');
            $table->string('title')->comment('Title for the day e.g., Trek to Dingboche');
            $table->longText('description')->comment('Detailed description for the day');
            $table->integer('max_altitude_m')->nullable()->comment('Altitude for that day');
            $table->string('meals')->nullable()->comment('Meals included e.g., B/L/D');
            $table->string('accommodation')->nullable()->comment('Where to stay that night');
            $table->string('overnight_at')->nullable()->comment('Location name');
            $table->integer('walking_hours')->nullable()->comment('Walking hours that day');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['package_id','day_number'])->comment('Ensure day numbers unique per package');
            $table->index('package_id');
        });
    }
    public function down(): void { Schema::dropIfExists('package_itineraries'); }
};

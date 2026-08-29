<?php
/**
 * Migration: Create Package FAQs Table
 * Purpose: FAQ per package (e.g., Do I need TIMS card? What about altitude sickness?).
 *          Shown in tabs on frontend package detail.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->string('question')->comment('FAQ question');
            $table->text('answer')->comment('FAQ answer (rich text)');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('package_id');
        });
        // Also package_equipment for gear list (Nepal specific)
        Schema::create('package_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->string('item')->comment('Gear item e.g., Down jacket');
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(true)->comment('Is this gear mandatory?');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('package_id');
        });
    }
    public function down(): void { Schema::dropIfExists('package_equipment'); Schema::dropIfExists('package_faqs'); }
};

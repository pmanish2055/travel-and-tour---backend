<?php
/**
 * Migration: Create Package Inclusions Table
 * Purpose: Stores both includes and excludes for packages (type column discriminates).
 *          E.g., Includes: Airport transfers, Excludes: International flights.
 *          Alternative to two separate tables - uses enum type.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_inclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->comment('FK to packages');
            $table->enum('type', ['include','exclude'])->default('include')->comment('Whether this is include or exclude');
            $table->string('title')->comment('Inclusion title e.g., All ground transportation');
            $table->text('description')->nullable()->comment('Optional detail');
            $table->string('icon')->nullable()->comment('Icon name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['package_id','type']);
        });
    }
    public function down(): void { Schema::dropIfExists('package_inclusions'); }
};

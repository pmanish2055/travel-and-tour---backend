<?php
/**
 * Migration: Create Payments and Coupons Tables
 * Purpose: Payments tracks gateway transactions (eSewa, Khalti, Stripe, bank).
 *          Coupons for discount codes.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete()->comment('FK to bookings');
            $table->enum('gateway', ['esewa','khalti','stripe','paypal','bank','cash'])->comment('Payment gateway');
            $table->string('transaction_id')->nullable()->unique()->comment('Gateway transaction ID');
            $table->decimal('amount', 10, 2)->comment('Paid amount');
            $table->enum('currency', ['NPR','USD'])->default('NPR');
            $table->enum('status', ['pending','completed','failed','refunded'])->default('pending');
            $table->json('raw_response')->nullable()->comment('Gateway raw JSON response for debug');
            $table->timestamps();
            $table->index('booking_id');
            $table->index('gateway');
        });
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Coupon code e.g., NEPAL10');
            $table->enum('discount_type', ['fixed','percent'])->default('percent');
            $table->decimal('value', 10, 2)->comment('Discount value');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('usage_limit')->nullable()->comment('Max uses');
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Pivot coupon_package if package-specific coupons needed
        Schema::create('coupon_package', function (Blueprint $table) {
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->primary(['coupon_id','package_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('coupon_package'); Schema::dropIfExists('coupons'); Schema::dropIfExists('payments'); }
};

<?php
/**
 * Migration: Create CMS Tables
 * Purpose: Stores static pages, sliders, team members, testimonials, partners,
 *          FAQs, Why Choose Us features - all for company setup / CMS.
 *          Filament Resources: PageResource, SliderResource, etc.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pages: About Us, Terms, Privacy, etc
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Page title');
            $table->string('slug')->unique()->comment('URL slug /pages/{slug}');
            $table->longText('content')->comment('HTML content');
            $table->string('template')->nullable()->comment('Blade template name');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('is_system')->default(false)->comment('System page cannot be deleted');
            $table->enum('status', ['draft','published'])->default('published');
            $table->timestamps();
            $table->softDeletes();
        });
        // Sliders: homepage hero
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Slide title');
            $table->string('subtitle')->nullable();
            $table->string('image')->comment('Slide image path');
            $table->string('cta_text')->nullable()->comment('Button text');
            $table->string('cta_link')->nullable()->comment('Button link');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Team members: guides, staff
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Member name');
            $table->string('designation')->comment('Role e.g., Trek Guide');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Testimonials / Reviews
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete()->comment('FK to packages - nullable for general review');
            $table->string('customer_name')->comment('Reviewer name');
            $table->string('customer_country')->nullable();
            $table->string('avatar')->nullable();
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->text('comment')->comment('Review text');
            $table->date('trip_date')->nullable()->comment('When they did the trip');
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->timestamps();
            $table->index('package_id');
            $table->index('status');
        });
        // Partners: associations (TAAN, NTB)
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Partner name');
            $table->string('logo')->comment('Logo path');
            $table->string('website')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // FAQs: global FAQs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->comment('FAQ question');
            $table->text('answer')->comment('Answer');
            $table->string('category')->nullable()->comment('Category e.g., Booking, Trekking');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        // Why Choose Us features
        Schema::create('why_choose_us', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Feature title e.g., Expert Guides');
            $table->text('description')->comment('Feature description');
            $table->string('icon')->nullable()->comment('Icon name');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('why_choose_us');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('pages');
    }
};

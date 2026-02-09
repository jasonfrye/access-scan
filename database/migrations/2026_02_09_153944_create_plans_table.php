<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Pro, Lifetime
            $table->string('slug')->unique();
            $table->string('stripe_price_id')->nullable(); // Monthly price ID
            $table->string('stripe_yearly_price_id')->nullable();
            $table->string('stripe_lifetime_price_id')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->decimal('price_lifetime', 10, 2)->default(0);
            $table->integer('scan_limit')->default(0); // 0 = unlimited
            $table->integer('page_limit_per_scan')->default(1);
            $table->integer('scheduled_scan_limit')->default(0);
            $table->boolean('has_pdf_export')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->json('features')->nullable(); // Array of feature strings
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};

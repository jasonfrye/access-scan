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
        Schema::create('pricing_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Default Pricing", "A/B Test A", etc.
            $table->string('description')->nullable();
            $table->json('config'); // Full pricing configuration
            $table->boolean('is_active')->default(false);
            $table->integer('traffic_split')->default(100); // For A/B testing
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_configs');
    }
};

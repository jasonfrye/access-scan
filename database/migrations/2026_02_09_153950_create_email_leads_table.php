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
        Schema::create('email_leads', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('source')->nullable(); // guest_scan, landing_page, etc.
            $table->foreignId('scan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('converted_at')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->boolean('is_subscribed')->default(true);
            $table->timestamps();

            $table->index(['source']);
            $table->index(['is_subscribed']);
            $table->index(['converted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_leads');
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null');
            $table->string('stripe_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_trial_ends_at')->nullable();
            $table->integer('scan_count')->default(0);
            $table->timestamp('scan_reset_at')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('email_notifications')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'plan_id',
                'stripe_id',
                'stripe_customer_id',
                'stripe_trial_ends_at',
                'scan_count',
                'scan_reset_at',
                'timezone',
                'email_notifications',
            ]);
        });
    }
};

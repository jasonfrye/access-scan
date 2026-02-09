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
        // Check if columns already exist from previous migration
        $columns = Schema::getColumnListing('users');
        
        if (!in_array('stripe_id', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('stripe_id')->nullable()->after('password');
            });
        }
        
        if (!in_array('plan_id', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('set null')->after('stripe_id');
            });
        }
        
        if (!in_array('stripe_customer_id', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('stripe_customer_id')->nullable()->after('stripe_id');
            });
        }
        
        if (!in_array('stripe_trial_ends_at', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('stripe_trial_ends_at')->nullable()->after('stripe_customer_id');
            });
        }
        
        if (!in_array('scan_count', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('scan_count')->default(0)->after('plan_id');
            });
        }
        
        if (!in_array('scan_reset_at', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('scan_reset_at')->nullable()->after('scan_count');
            });
        }
        
        if (!in_array('timezone', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('timezone')->default('UTC')->after('scan_reset_at');
            });
        }
        
        if (!in_array('email_notifications', $columns)) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('email_notifications')->default(true)->after('timezone');
            });
        }
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

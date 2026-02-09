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
            $table->string('stripe_id')->nullable()->after('password');
            $table->string('plan')->default('free')->after('stripe_id');
            $table->unsignedInteger('scan_count')->default(0)->after('plan');
            $table->unsignedInteger('scan_limit')->default(1)->after('scan_count');
            $table->timestamp('trial_ends_at')->nullable()->after('scan_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['stripe_id', 'plan', 'scan_count', 'scan_limit', 'trial_ends_at']);
        });
    }
};

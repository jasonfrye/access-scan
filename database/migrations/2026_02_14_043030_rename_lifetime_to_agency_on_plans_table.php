<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['stripe_lifetime_price_id', 'price_lifetime']);
        });

        // Convert any existing lifetime plans to agency
        DB::table('plans')->where('slug', 'lifetime')->update(['slug' => 'agency', 'name' => 'Agency']);

        // Convert any existing lifetime users to agency
        DB::table('users')->where('plan', 'lifetime')->update(['plan' => 'agency']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('stripe_lifetime_price_id')->nullable()->after('stripe_yearly_price_id');
            $table->decimal('price_lifetime', 10, 2)->default(0)->after('price_yearly');
        });

        DB::table('plans')->where('slug', 'agency')->update(['slug' => 'lifetime', 'name' => 'Lifetime']);
        DB::table('users')->where('plan', 'agency')->update(['plan' => 'lifetime']);
    }
};

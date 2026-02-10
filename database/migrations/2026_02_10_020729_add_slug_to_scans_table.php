<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->uuid('slug')->nullable()->unique()->after('id');
        });

        // Backfill existing scans with UUIDs
        DB::table('scans')->whereNull('slug')->eachById(function ($scan) {
            DB::table('scans')->where('id', $scan->id)->update(['slug' => Str::uuid()->toString()]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

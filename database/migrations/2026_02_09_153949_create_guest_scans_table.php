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
        Schema::create('guest_scans', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->string('email')->nullable();
            $table->foreignId('scan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamp('email_captured_at')->nullable();
            $table->timestamps();

            $table->index(['ip_address', 'scanned_at']);
            $table->index(['email']);
            $table->index(['scan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_scans');
    }
};

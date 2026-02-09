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
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('url');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->enum('scan_type', ['quick', 'full', 'scheduled'])->default('quick');
            $table->integer('pages_scanned')->default(0);
            $table->integer('issues_found')->default(0);
            $table->integer('errors_count')->default(0);
            $table->integer('warnings_count')->default(0);
            $table->integer('notices_count')->default(0);
            $table->decimal('score', 5, 2)->nullable(); // 0-100
            $table->string('grade')->nullable(); // A, B, C, D, F
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // For cache expiration
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('url');
            $table->index('status');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};

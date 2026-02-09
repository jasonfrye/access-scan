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
        Schema::create('scan_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->enum('status', ['pending', 'scanning', 'completed', 'failed'])->default('pending');
            $table->integer('issues_count')->default(0);
            $table->integer('errors_count')->default(0);
            $table->integer('warnings_count')->default(0);
            $table->integer('notices_count')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->string('page_title')->nullable();
            $table->integer('http_status')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();

            $table->index(['scan_id', 'url']);
            $table->index(['scan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_pages');
    }
};

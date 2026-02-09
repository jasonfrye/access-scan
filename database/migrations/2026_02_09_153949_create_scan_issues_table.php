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
        Schema::create('scan_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_page_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['error', 'warning', 'notice'])->default('error');
            $table->string('code')->nullable(); // WCAG code e.g., WCAG2AA.Principle1.Guideline1_1.1_1_1.H37
            $table->text('message');
            $table->text('context')->nullable(); // HTML snippet
            $table->string('selector')->nullable(); // CSS selector for the element
            $table->string('wcag_principle')->nullable(); // Perceivable, Operable, Understandable, Robust
            $table->string('wcag_guideline')->nullable();
            $table->string('wcag_level')->nullable(); // A, AA, AAA
            $table->text('recommendation')->nullable();
            $table->boolean('is_fixed')->default(false);
            $table->boolean('is_ignored')->default(false);
            $table->timestamps();

            $table->index(['scan_page_id', 'type']);
            $table->index(['scan_page_id', 'code']);
            $table->index(['scan_page_id', 'is_fixed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_issues');
    }
};

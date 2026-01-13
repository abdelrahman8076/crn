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
        Schema::create('targets', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Target metrics
            $table->integer('target_total');         // e.g., 100
            $table->integer('target_remaining');     // live counter

            // Period and Classification
            // Note: Added an index to 'period' as you'll likely query by date often
            $table->string('period')->index();       // 2026-01, 2026-Q1, etc
            $table->string('type')->default('deal'); // deal | amount | custom

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
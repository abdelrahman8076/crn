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
        Schema::create('model_permissions', function (Blueprint $table) {
            $table->id();
            $table->morphs('model'); // Creates model_id and model_type (polymorphic)
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->boolean('granted')->default(true); // true = granted, false = denied (override)
            $table->timestamps();
            
            $table->unique(['model_id', 'model_type', 'permission_id'], 'model_permission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_permissions');
    }
};

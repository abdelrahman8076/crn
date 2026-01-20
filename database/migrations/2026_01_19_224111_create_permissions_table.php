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
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // e.g., 'view_clients', 'create_users'
                $table->string('slug')->unique(); // e.g., 'view-clients', 'create-users'
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            // Table exists, ensure it has the required columns
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};

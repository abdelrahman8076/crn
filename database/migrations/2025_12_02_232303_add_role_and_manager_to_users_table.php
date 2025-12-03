<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Add role_id (Admin / Manager / Sales)
            $table->foreignId('role_id')
                ->after('password')
                ->constrained('roles')
                ->cascadeOnDelete();

            // Manager for sales team (nullable for Admins & Managers)
            $table->foreignId('manager_id')
                ->nullable()
                ->after('role_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            $table->dropForeign(['manager_id']);
            $table->dropColumn('manager_id');
        });
    }
};

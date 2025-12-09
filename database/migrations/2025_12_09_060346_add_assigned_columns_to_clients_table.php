<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to_sale')->nullable()->after('address');
            $table->unsignedBigInteger('assigned_to_manager')->nullable()->after('assigned_to_sale');

            $table->foreign('assigned_to_sale')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_to_manager')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_sale']);
            $table->dropForeign(['assigned_to_manager']);

            $table->dropColumn(['assigned_to_sale', 'assigned_to_manager']);
        });
    }
};

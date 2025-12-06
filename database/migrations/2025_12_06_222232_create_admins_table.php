<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();

            // Hashed password
            $table->string('password');

            // Optional role column for permissions

            // If you have a roles table, uncomment this:
            // $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Recommended for admin accounts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

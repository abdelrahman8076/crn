<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get Admin role ID first
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        
        if ($adminRole) {
            // Set role_id to null for users with Admin role
            DB::table('users')->where('role_id', $adminRole->id)->update(['role_id' => null]);
            
            // Delete Admin role from roles table
            DB::table('roles')->where('name', 'Admin')->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate Admin role if needed
        DB::table('roles')->insert([
            'name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};

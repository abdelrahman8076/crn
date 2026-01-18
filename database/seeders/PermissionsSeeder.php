<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'users.view', 'group' => 'users', 'description' => 'View users'],
            ['name' => 'users.create', 'group' => 'users', 'description' => 'Create users'],
            ['name' => 'users.update', 'group' => 'users', 'description' => 'Update users'],
            ['name' => 'users.delete', 'group' => 'users', 'description' => 'Delete users'],
            ['name' => 'users.manage_all', 'group' => 'users', 'description' => 'Manage all users regardless of hierarchy'],
            
            // Admin Management
            ['name' => 'admins.view', 'group' => 'admins', 'description' => 'View admins'],
            ['name' => 'admins.create', 'group' => 'admins', 'description' => 'Create admins'],
            ['name' => 'admins.update', 'group' => 'admins', 'description' => 'Update admins'],
            ['name' => 'admins.delete', 'group' => 'admins', 'description' => 'Delete admins'],
            ['name' => 'admins.manage_all', 'group' => 'admins', 'description' => 'Manage all admins regardless of hierarchy'],
            
            // Client Management
            ['name' => 'clients.view', 'group' => 'clients', 'description' => 'View clients'],
            ['name' => 'clients.create', 'group' => 'clients', 'description' => 'Create clients'],
            ['name' => 'clients.update', 'group' => 'clients', 'description' => 'Update clients'],
            ['name' => 'clients.delete', 'group' => 'clients', 'description' => 'Delete clients'],
            ['name' => 'clients.manage_all', 'group' => 'clients', 'description' => 'Manage all clients'],
            
            // Deal Management
            ['name' => 'deals.view', 'group' => 'deals', 'description' => 'View deals'],
            ['name' => 'deals.create', 'group' => 'deals', 'description' => 'Create deals'],
            ['name' => 'deals.update', 'group' => 'deals', 'description' => 'Update deals'],
            ['name' => 'deals.delete', 'group' => 'deals', 'description' => 'Delete deals'],
            ['name' => 'deals.manage_all', 'group' => 'deals', 'description' => 'Manage all deals'],
            
            // Task Management
            ['name' => 'tasks.view', 'group' => 'tasks', 'description' => 'View tasks'],
            ['name' => 'tasks.create', 'group' => 'tasks', 'description' => 'Create tasks'],
            ['name' => 'tasks.update', 'group' => 'tasks', 'description' => 'Update tasks'],
            ['name' => 'tasks.delete', 'group' => 'tasks', 'description' => 'Delete tasks'],
            ['name' => 'tasks.manage_all', 'group' => 'tasks', 'description' => 'Manage all tasks'],
            
            // Lead Management
            ['name' => 'leads.view', 'group' => 'leads', 'description' => 'View leads'],
            ['name' => 'leads.create', 'group' => 'leads', 'description' => 'Create leads'],
            ['name' => 'leads.update', 'group' => 'leads', 'description' => 'Update leads'],
            ['name' => 'leads.delete', 'group' => 'leads', 'description' => 'Delete leads'],
            ['name' => 'leads.manage_all', 'group' => 'leads', 'description' => 'Manage all leads'],
            
            // Note Management
            ['name' => 'notes.view', 'group' => 'notes', 'description' => 'View notes'],
            ['name' => 'notes.create', 'group' => 'notes', 'description' => 'Create notes'],
            ['name' => 'notes.update', 'group' => 'notes', 'description' => 'Update notes'],
            ['name' => 'notes.delete', 'group' => 'notes', 'description' => 'Delete notes'],
            ['name' => 'notes.manage_all', 'group' => 'notes', 'description' => 'Manage all notes'],
            
            // Dashboard & Reports
            ['name' => 'dashboard.view', 'group' => 'dashboard', 'description' => 'View dashboard'],
            ['name' => 'reports.view', 'group' => 'reports', 'description' => 'View reports'],
            ['name' => 'reports.export', 'group' => 'reports', 'description' => 'Export reports'],
            
            // System Management
            ['name' => 'roles.manage', 'group' => 'system', 'description' => 'Manage roles'],
            ['name' => 'permissions.manage', 'group' => 'system', 'description' => 'Manage permissions'],
            ['name' => 'positions.manage', 'group' => 'system', 'description' => 'Manage positions/hierarchy'],
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}

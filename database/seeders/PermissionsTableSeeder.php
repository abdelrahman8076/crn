<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Access Permissions
            ['name' => 'Access Admin Panel', 'slug' => 'access-admin', 'description' => 'Can login and access admin panel'],
            ['name' => 'Access User Side', 'slug' => 'access-user-side', 'description' => 'Can access user login side (/login)'],
            
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view users list'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users'],
            
            // Admin Management
            ['name' => 'View Admins', 'slug' => 'view-admins', 'description' => 'Can view admins list'],
            ['name' => 'Create Admins', 'slug' => 'create-admins', 'description' => 'Can create new admins'],
            ['name' => 'Edit Admins', 'slug' => 'edit-admins', 'description' => 'Can edit existing admins'],
            ['name' => 'Delete Admins', 'slug' => 'delete-admins', 'description' => 'Can delete admins'],
            
            // Client Management
            ['name' => 'View Clients', 'slug' => 'view-clients', 'description' => 'Can view clients list'],
            ['name' => 'Create Clients', 'slug' => 'create-clients', 'description' => 'Can create new clients'],
            ['name' => 'Edit Clients', 'slug' => 'edit-clients', 'description' => 'Can edit existing clients'],
            ['name' => 'Delete Clients', 'slug' => 'delete-clients', 'description' => 'Can delete clients'],
            
            // Lead Management
            ['name' => 'View Leads', 'slug' => 'view-leads', 'description' => 'Can view leads list'],
            ['name' => 'Create Leads', 'slug' => 'create-leads', 'description' => 'Can create new leads'],
            ['name' => 'Edit Leads', 'slug' => 'edit-leads', 'description' => 'Can edit existing leads'],
            ['name' => 'Delete Leads', 'slug' => 'delete-leads', 'description' => 'Can delete leads'],
            
            // Deal Management
            ['name' => 'View Deals', 'slug' => 'view-deals', 'description' => 'Can view deals list'],
            ['name' => 'Create Deals', 'slug' => 'create-deals', 'description' => 'Can create new deals'],
            ['name' => 'Edit Deals', 'slug' => 'edit-deals', 'description' => 'Can edit existing deals'],
            ['name' => 'Delete Deals', 'slug' => 'delete-deals', 'description' => 'Can delete deals'],
            
            // Task Management
            ['name' => 'View Tasks', 'slug' => 'view-tasks', 'description' => 'Can view tasks list'],
            ['name' => 'Create Tasks', 'slug' => 'create-tasks', 'description' => 'Can create new tasks'],
            ['name' => 'Edit Tasks', 'slug' => 'edit-tasks', 'description' => 'Can edit existing tasks'],
            ['name' => 'Delete Tasks', 'slug' => 'delete-tasks', 'description' => 'Can delete tasks'],
            
            // Target Management
            ['name' => 'View Targets', 'slug' => 'view-targets', 'description' => 'Can view targets'],
            ['name' => 'Create Targets', 'slug' => 'create-targets', 'description' => 'Can create new targets'],
            ['name' => 'Edit Targets', 'slug' => 'edit-targets', 'description' => 'Can edit existing targets'],
            ['name' => 'Delete Targets', 'slug' => 'delete-targets', 'description' => 'Can delete targets'],
            
            // Notes Management
            ['name' => 'View Notes', 'slug' => 'view-notes', 'description' => 'Can view notes list'],
            ['name' => 'Create Notes', 'slug' => 'create-notes', 'description' => 'Can create new notes'],
            ['name' => 'Edit Notes', 'slug' => 'edit-notes', 'description' => 'Can edit existing notes'],
            ['name' => 'Delete Notes', 'slug' => 'delete-notes', 'description' => 'Can delete notes'],
            
            // Dashboard & Reports
            ['name' => 'View Dashboard', 'slug' => 'view-dashboard', 'description' => 'Can view dashboard'],
            ['name' => 'View Reports', 'slug' => 'view-reports', 'description' => 'Can view reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        // Assign default permissions to roles
        $managerRole = Role::where('name', 'Manager')->first();
        $salesRole = Role::where('name', 'Sales')->first();

        if ($managerRole) {
            // Managers get most permissions except user and admin management, including admin access and user-side access
            $managerPermissions = Permission::whereNotIn('slug', [
                'create-users', 'edit-users', 'delete-users',
                'view-admins', 'create-admins', 'edit-admins', 'delete-admins'
            ])->pluck('id');
            // Ensure access-user-side is included
            $userSidePermission = Permission::where('slug', 'access-user-side')->first();
            if ($userSidePermission && !$managerPermissions->contains($userSidePermission->id)) {
                $managerPermissions->push($userSidePermission->id);
            }
            $managerRole->permissions()->sync($managerPermissions);
        }

        if ($salesRole) {
            // Sales get limited permissions, including admin access and user-side access
            $salesPermissions = Permission::whereIn('slug', [
                'access-admin', // Sales can access admin panel
                'access-user-side', // Sales can access user login side
                'view-clients', 'create-clients', 'edit-clients',
                'view-leads', 'create-leads', 'edit-leads',
                'view-deals', 'create-deals', 'edit-deals',
                'view-tasks', 'create-tasks', 'edit-tasks',
                'view-notes', 'create-notes', 'edit-notes',
                'view-targets', 'view-dashboard'
            ])->pluck('id');
            $salesRole->permissions()->sync($salesPermissions);
        }
    }
}

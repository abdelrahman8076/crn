# Unified Authorization System Implementation Summary

## ✅ Completed Components

### 1. Database Schema
- ✅ `permissions` table - Stores all system permissions
- ✅ `role_permissions` table - Links roles to permissions
- ✅ `model_roles` table - Polymorphic table for assigning roles to Admin/User
- ✅ `model_permissions` table - Polymorphic table for direct permission overrides
- ✅ `positions` table - Hierarchical position system with parent_id
- ✅ Added `position_id` to `admins` table
- ✅ Added `position_id` to `users` table

### 2. Models Created
- ✅ `Permission` model with relationships
- ✅ `ModelRole` model (polymorphic)
- ✅ `ModelPermission` model (polymorphic)
- ✅ `Position` model with hierarchy methods
- ✅ Updated `Role` model with permissions relationship
- ✅ Updated `Admin` model with HasPermissions trait
- ✅ Updated `User` model with HasPermissions trait

### 3. Authorization System
- ✅ `HasPermissions` trait - Unified authorization for Admin and User
- ✅ `AuthorizationService` - Service class for permission checks
- ✅ `RequirePermission` middleware - Single permission check
- ✅ `RequireAnyPermission` middleware - Multiple permission check
- ✅ `UserPolicy` - Example policy using new permission system

### 4. Seeders
- ✅ `PermissionsSeeder` - Seeds all system permissions
- ✅ `PositionsSeeder` - Seeds initial hierarchy structure

## 🔄 Next Steps Required

### 1. Register Middleware
Add to `app/Http/Kernel.php`:
```php
protected $middlewareAliases = [
    // ... existing middleware
    'permission' => \App\Http\Middleware\RequirePermission::class,
    'permission.any' => \App\Http\Middleware\RequireAnyPermission::class,
];
```

### 2. Update Controllers
- Update `AdminController` to:
  - Accept `role_ids[]` and `permission_ids[]` in create/update
  - Assign roles and permissions using `$admin->syncRoles()` and `$admin->syncPermissions()`
  - Filter manageable admins using `AuthorizationService`

- Update `UsersController` to:
  - Accept `role_ids[]`, `permission_ids[]`, and `position_id` in create/update
  - Assign roles and permissions
  - Filter manageable users using hierarchy

### 3. Update Views
- Update admin create/edit forms to include:
  - Role selection (multi-select)
  - Permission selection (multi-select)
  - Position selection (dropdown)

- Update user create/edit forms to include:
  - Role selection (multi-select)
  - Permission selection (multi-select)
  - Position selection (dropdown)

### 4. Update Sidebar
- Replace role-name checks with permission checks:
  ```php
  // OLD: @if($isAdmin || $isManager)
  // NEW: @if(auth()->user()->hasPermission('clients.view'))
  ```

### 5. Update Existing Middleware
- Update `AllowAdminOrManager` to use permissions
- Update `HasAccessFilter` trait to use new permission system

### 6. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed --class=PermissionsSeeder
php artisan db:seed --class=PositionsSeeder
```

### 7. Assign Initial Roles & Permissions
Create a seeder or admin interface to:
- Assign permissions to existing roles
- Assign roles to existing admins/users
- Assign positions to existing admins/users

## 📋 Key Features

### Unified Authorization
- Both `Admin` and `User` use the same `HasPermissions` trait
- Same permission checking methods work for both
- No hardcoded role checks in code

### Hierarchy System
- Fully dynamic hierarchy via `positions` table
- `canManage()` method checks hierarchy automatically
- `getManageableModels()` returns all subordinates

### Permission Overrides
- Direct permissions override role permissions
- Can grant or deny specific permissions per user/admin
- Supports complex permission scenarios

### Scalable Design
- Add new permissions without code changes
- Add new positions without code changes
- Add new roles without code changes

## 🔐 Permission Naming Convention

Format: `{resource}.{action}`

Examples:
- `users.view` - View users
- `users.create` - Create users
- `users.manage_all` - Manage all users (bypasses hierarchy)
- `clients.view` - View clients
- `dashboard.view` - View dashboard

## 🏗️ Hierarchy Example

```
CEO (level 0)
  └── VP (level 1)
      └── Director (level 2)
          └── Manager (level 3)
              └── Sales (level 4)
                  └── Staff (level 5)
```

A Manager can manage Sales and Staff, but not Director or VP.

## 📝 Usage Examples

### Check Permission
```php
if ($user->hasPermission('users.view')) {
    // Allow access
}
```

### Check Multiple Permissions
```php
if ($user->hasAnyPermission(['users.view', 'users.create'])) {
    // Allow access
}
```

### Check Hierarchy
```php
if ($currentUser->canManage($targetUser)) {
    // Can manage this user
}
```

### Assign Roles
```php
$admin->syncRoles([1, 2, 3]); // Role IDs
$admin->assignRole('Admin'); // Role name
```

### Assign Permissions
```php
$user->syncPermissions([1, 2, 3]); // Permission IDs
$user->grantPermission('users.view');
```

### Middleware Usage
```php
Route::get('/users', [UsersController::class, 'index'])
    ->middleware('permission:users.view');
```

## ⚠️ Important Notes

1. **Legacy Support**: The `role_id` column on `users` table is kept for backward compatibility but should be migrated to use `model_roles` table.

2. **Migration Path**: Existing code using `$user->role->name` will need to be updated to use `$user->hasRole('RoleName')` or permission checks.

3. **Position Assignment**: Both admins and users need positions assigned for hierarchy to work. Consider creating a migration to assign default positions.

4. **Initial Setup**: After running migrations, you'll need to:
   - Assign permissions to roles
   - Assign roles to users/admins
   - Assign positions to users/admins

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'manager_id', // <-- used for sales team under managers
    ];
    protected $casts = [
        'created_at' => 'date:Y-m-d',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // 1. Dissociate Sales Team: If this user is a manager, 
            // set their team members' manager_id to null so they aren't deleted.
            User::where('manager_id', $user->id)->update(['manager_id' => null]);

            // 2. Dissociate Clients: Set all assignment fields to null 
            // so we don't lose client records when a staff member leaves.
            // Handle both old 'assigned_to' column and new 'assigned_to_sale'/'assigned_to_manager' columns
            DB::table('clients')
                ->where(function($query) use ($user) {
                    $query->where('assigned_to', $user->id)
                          ->orWhere('assigned_to_sale', $user->id)
                          ->orWhere('assigned_to_manager', $user->id);
                })
                ->update([
                    'assigned_to' => null,
                    'assigned_to_sale' => null,
                    'assigned_to_manager' => null
                ]);

            // 3. Dissociate Leads: Set assigned_to to null
            DB::table('leads')
                ->where('assigned_to', $user->id)
                ->update(['assigned_to' => null]);

            // 4. Handle Notes: Delete notes that reference this user
            // The notes table may have either user_id or assigned_to column with foreign key
            // Based on the error, it seems assigned_to has a foreign key constraint that prevents null
            // So we delete the notes instead of trying to set to null
            if (Schema::hasColumn('notes', 'user_id')) {
                DB::table('notes')->where('user_id', $user->id)->delete();
            }
            if (Schema::hasColumn('notes', 'assigned_to')) {
                // Delete notes with assigned_to foreign key constraint
                DB::table('notes')->where('assigned_to', $user->id)->delete();
            }

            // 5. Delete dependent records: Tasks usually belong 
            // only to the creator, so we delete them.
            $user->tasks()->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // User belongs to a role (Admin / Manager / Sales)
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // A manager has many sales under him
    public function salesTeam()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    // A sales/user belongs to a manager
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // User (sales or manager) has assigned clients
    public function clients()
    {
        return $this->hasMany(Client::class, 'assigned_to');
    }

    // User has many tasks he created or assigned
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function targets()
    {
        return $this->hasMany(Target::class);
    }

    public function activeTarget()
    {
        return $this->hasOne(Target::class)->where('is_active', true);
    }

    // User adds many notes
    // public function notes()
    // {
    //     return $this->hasMany(Note::class, 'assigned_to');
    // }
    public function scopeSales($query)
    {
        return $query->whereRelation('role', 'name', 'Sales');
    }

    public function scopeManagers($query)
    {
        return $query->whereRelation('role', 'name', 'Manager');
    }
    public function team()
    {
        return $this->hasMany(User::class, 'manager_id'); // Adjust 'manager_id' as your DB column
    }
    public function hasRole(string $role): bool
    {
        // Option A: If you have a 'role' relationship with a 'name' column
        return $this->role && $this->role->name === $role;

        // Option B: If you just have a 'role' string column on the users table
        // return $this->role === $role;
    }

    /**
     * Check if user has a specific permission through their role.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->hasPermission($permissionSlug);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->whereIn('slug', $permissionSlugs)
            ->exists();
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissionSlugs): bool
    {
        if (!$this->role) {
            return false;
        }

        $count = $this->role->permissions()
            ->whereIn('slug', $permissionSlugs)
            ->count();

        return $count === count($permissionSlugs);
    }
    
}

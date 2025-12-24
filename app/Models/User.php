<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
    'created_at'=> 'date:Y-m-d',
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
        return $this->hasMany(Task::class, 'user_id');
    }

    // User adds many notes
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }
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


}

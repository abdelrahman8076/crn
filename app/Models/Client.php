<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'address',
        'source','status',
        'assigned_to_sale',
        'assigned_to_manager',
        'feedback'
    ];
    protected function status(): Attribute
{
    return Attribute::make(
        get: fn ($value) => ucwords(str_replace('_', ' ', $value)),
        
        // Optional: Ensure it's saved as snake_case in the DB
        set: fn ($value) => strtolower(str_replace(' ', '_', $value)),
    );
}

    // Relationship for Sales/sale
    public function assignedSale()
    {
        return $this->belongsTo(User::class, 'assigned_to_sale');
    }

    // Relationship for Manager
    public function assignedManager()
    {
        return $this->belongsTo(User::class, 'assigned_to_manager');
    }

    // Leads related to this client
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    // Tasks related to this client
    public function tasks()
    {
        return $this->morphMany(Task::class, 'related');
    }

    // Notes related to this client
    public function notes()
    {
        return $this->morphMany(Note::class, 'related');
    }
}

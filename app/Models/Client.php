<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'address',
        'assigned_to_sale',
        'assigned_to_manager',
    ];

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

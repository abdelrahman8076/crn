<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    use HasFactory;

    protected $fillable = ['deal_name','amount','stage','client_id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class,'related');
    }

    public function notes()
    {
        return $this->morphMany(Note::class,'related');
    }
}

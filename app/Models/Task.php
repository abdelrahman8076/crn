<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title','due_date','status','assigned_to','description'];
 protected $casts = [
    'due_date' => 'date:Y-m-d',
    'created_at'=> 'date:Y-m-d',
];



    public function related()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class,'assigned_to');
    }
}

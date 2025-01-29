<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'assigned_to', 'created_by', 'completed'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'body', 'author', 'project_id', 'done'];

    // Eloquent Relation

    public function owner()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
}

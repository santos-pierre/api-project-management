<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'deadline', 'slug', 'description', 'repository_url', 'status_project_id'];

    // Eloquent Relation
    public function status()
    {
        return $this->belongsTo(StatusProject::class, 'status_project_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }
}

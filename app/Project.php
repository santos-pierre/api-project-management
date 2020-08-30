<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'deadline', 'slug', 'description', 'repository_url', 'status_project_id'];

    // Eloquent Relation
    public function status()
    {
        return $this->belongsTo('App\StatusProject', 'status_project_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'author', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StatusProject extends Model
{
    use HasFactory;

    public function projects()
    {
        return $this->hasMany(Project::class, 'status_project_id', 'id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusProject extends Model
{
    public function projects()
    {
        return $this->hasMany('App\Project', 'status_project_id', 'id');
    }
}

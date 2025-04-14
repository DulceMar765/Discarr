<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function costs()
    {
        return $this->hasMany(ProjectCost::class);
    }
}

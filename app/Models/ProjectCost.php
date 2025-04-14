<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

class ProjectCost extends Model
{
    protected $fillable = ['project_id', 'supplier_id', 'amount', 'description', 'date', 'type'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

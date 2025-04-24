<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialProject extends Model
{
    protected $table = 'material_project';
    protected $fillable = [
        'project_id', 'material_id', 'quantity', 'date'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}

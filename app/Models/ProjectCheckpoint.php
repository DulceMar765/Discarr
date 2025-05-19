<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCheckpoint extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'date',
        'status',
        'completion_percentage'
    ];
    
    protected $casts = [
        'date' => 'date',
        'completion_percentage' => 'decimal:2'
    ];
    
    /**
     * Obtener el proyecto al que pertenece este checkpoint
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    /**
     * Obtener una clase CSS basada en el estado del checkpoint
     */
    public function getStatusClassAttribute()
    {
        return [
            'pendiente' => 'bg-secondary',
            'en_progreso' => 'bg-info',
            'completado' => 'bg-success',
            'retrasado' => 'bg-danger'
        ][$this->status] ?? 'bg-secondary';
    }
    
    /**
     * Obtener el nombre formateado del estado
     */
    public function getStatusNameAttribute()
    {
        return [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'completado' => 'Completado',
            'retrasado' => 'Retrasado'
        ][$this->status] ?? 'Desconocido';
    }
}

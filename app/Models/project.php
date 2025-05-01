<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'budget',
        'token'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2'
    ];
    
    public function costs()
    {
        return $this->hasMany(ProjectCost::class);
    }
    
    public function projectEmployees()
    {
        return $this->hasMany(ProjectEmployee::class);
    }
    
    public function materialProjects()
    {
        return $this->hasMany(MaterialProject::class);
    }
    
    // Generar un token único para el proyecto
    public static function generateUniqueToken()
    {
        $token = bin2hex(random_bytes(16)); // Genera un token hexadecimal de 32 caracteres
        
        // Verifica que el token sea único
        while (self::where('token', $token)->exists()) {
            $token = bin2hex(random_bytes(16));
        }
        
        return $token;
    }
}

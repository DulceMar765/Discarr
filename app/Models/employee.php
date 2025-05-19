<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'salary',
        'hire_date',
        'address',
        'status',
        'on_vacation',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'salary' => 'decimal:2',
        'status' => 'boolean',
        'on_vacation' => 'boolean', // ✅ agregado
    ];

    public function vacations()
   {
    return $this->hasMany(Vacation::class);
   }

    // Método para saber si el empleado está de vacaciones
    public function isOnVacation($date = null)
    {
        $date = $date ?: Carbon::today();

        return $this->vacations()
            ->where('status', 'aprobado')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

}


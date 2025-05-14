<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class CalendarDay extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla
    protected $table = 'calendar_days';

    // Agrega los campos que se pueden asignar masivamente
    protected $fillable = [
        'date',
        'availability_status',
        'booked_slots',
        'total_slots',
        'manual_override',
        'available_slots',
    ];

    // Casteo de atributos
    protected $casts = [
        'available_slots' => 'array',
    ];

    // Relación con las citas
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'calendar_day_id');
    }

    // Método para obtener los horarios disponibles si no están definidos
    public function getCalculatedAvailableSlotsAttribute()
    {
        // Comprobar si ya hay horarios personalizados definidos
        $customSlots = $this->attributes['available_slots'] ? json_decode($this->attributes['available_slots'], true) : null;
        
        // Si hay horarios personalizados, respetarlos
        if (!empty($customSlots)) {
            \Log::info("Usando horarios personalizados para la fecha {$this->date}: " . json_encode($customSlots));
            return $customSlots;
        }
        
        // Si no hay horarios personalizados, calcularlos
        $allSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00']; // Horarios predeterminados
        $bookedSlots = $this->appointments->pluck('time_slot')->toArray();
        $availableSlots = array_diff($allSlots, $bookedSlots); // Devuelve los horarios que no están reservados
        
        \Log::info("Generando horarios calculados para la fecha {$this->date}: " . json_encode($availableSlots));
        return array_values($availableSlots);
    }
}

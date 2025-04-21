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
    ];

    // Relación con las citas
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'calendar_day_id');
    }

    // Método para obtener los horarios disponibles
    public function getAvailableSlotsAttribute()
    {
        $allSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00']; // Ejemplo de horarios
        $bookedSlots = $this->appointments->pluck('time_slot')->toArray();

        return array_diff($allSlots, $bookedSlots); // Devuelve los horarios que no están reservados
    }
}

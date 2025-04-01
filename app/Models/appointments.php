<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appointments extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'calendar_day_id',
        'time_slot',
    ];

    /**
     * Relación con el modelo `CalendarDay`.
     * Un appointment pertenece a un día del calendario.
     */
    public function calendarDay()
    {
        return $this->belongsTo(CalendarDay::class, 'calendar_day_id');
    }

    /**
     * Relación con el modelo `User`.
     * Un appointment pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

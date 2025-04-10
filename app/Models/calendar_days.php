<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calendar_days extends Model
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
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_day_id',
        'time_slot',
        'status',
        'description',
        'requester_name',
        'requester_email',
        'requester_phone',
    ];

    // Relación con el modelo User

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calendarDay()
    {
        return $this->belongsTo(CalendarDay::class, 'calendar_day_id');
    }
}

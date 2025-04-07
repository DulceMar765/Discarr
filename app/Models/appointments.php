<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appointments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calendar_day_id',
        'time_slot',
        'status',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el modelo CalendarDays
    public function calendarDay()
    {
        return $this->belongsTo(calendar_days::class);
    }
}

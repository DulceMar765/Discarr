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
        'description',
    ];

    // Relación con el modelo User

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calendarDay()
    {
        return $this->belongsTo(calendar_days::class, 'calendar_day_id');
    }
}

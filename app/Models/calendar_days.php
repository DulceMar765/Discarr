<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calendar_days extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'availability_status',
        'booked_slots',
        'total_slots',
    ];
}

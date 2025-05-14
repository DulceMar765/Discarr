<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    /** @use HasFactory<\Database\Factories\VacationFactory> */
     use HasFactory;

    protected $fillable = [
        'employee_id', 'start_date', 'end_date', 'reason', 'status'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'date_birth', 'address', 'priority'
    ];
}

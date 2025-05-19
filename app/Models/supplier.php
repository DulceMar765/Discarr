<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    // App\Models\Supplier.php
protected $fillable = [
    'name',
    'contact_name',
    'email',
    'phone_number',
    'address',
    'website',
    'priority',
    'reliability_score'
];

}

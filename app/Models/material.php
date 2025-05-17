<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'name',
        'description',
        'stock',
        'unit',
        'price',
        'category_id',
        'supplier_id',
    ];
}

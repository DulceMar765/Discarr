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

    // Relación con Category (pertenece a)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con Supplier (pertenece a)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legalizacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_legalizacion',
        'estado_depart',
        'estado_postg',
        'estado_tesoreria',
        'estado_contabilidad',
        'practica_id',
    ];

    // Relaciones
    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}

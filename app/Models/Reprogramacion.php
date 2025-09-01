<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reprogramacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_reprogramacion',
        'estado_reprogramacion',
        'tipo_reprogramacion',
        'estado_vice',
        'justificacion',
        'practica_id',
    ];

    // Relaciones
    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}

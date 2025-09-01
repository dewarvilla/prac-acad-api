<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'origen_lat',
        'origen_lng',
        'destino_lat',
        'destino_lng',
        'numero_recorridos',
        'numero_peajes',
        'valor_peajes',
        'valor_total_peajes',
        'distancia_trayectos_km',
        'distancia_total_km',
        'ruta_salida',
        'ruta_llegada',
        'practica_id',
    ];

    // Relaciones
    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}

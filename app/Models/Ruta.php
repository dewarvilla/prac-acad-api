<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'estado',
        'latitud_salidas',
        'latitud_llegadas',
        'numero_recorridos',
        'numero_peajes',
        'valor_peajes',
        'distancia_trayectos_km',
        'ruta_salida',
        'ruta_llegada',
        'programacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'numero_recorridos'     => 'integer',
        'numero_peajes'         => 'integer',
        'valor_peajes'          => 'decimal:2',
        'distancia_trayectos_km'=> 'decimal:2',
        'fechacreacion'         => 'datetime',
        'fechamodificacion'     => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

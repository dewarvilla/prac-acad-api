<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    use HasFactory;

    protected $table = 'ajustes';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'fecha_ajuste',
        'estado_ajuste', // 'aprobada'|'rechazada'|'pendiente'
        'estado_vice',           // 'aprobada'|'rechazada'|'pendiente'
        'estado_jefe_depart',           // 'aprobada'|'rechazada'|'pendiente'
        'estado_coordinador_postg',           // 'aprobada'|'rechazada'|'pendiente'
        'justificacion',
        'programacion_id',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_ajuste' => 'date',
        'fechacreacion' => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    // Relaciones
    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

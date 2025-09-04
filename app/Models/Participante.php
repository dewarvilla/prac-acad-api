<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    use HasFactory;

    protected $table = 'participantes';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'numero_identificacion',
        'discapacidad',
        'nombre',
        'correo_institucional',
        'telefono',
        'programa_academico',
        'facultad',
        'repitente',
        'tipo_participante', // 'estudiante' | 'docente' | 'acompanante'
        'programacion_id',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'discapacidad' => 'boolean',
        'repitente' => 'boolean',
        'fechacreacion' => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    // Relaciones
    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}


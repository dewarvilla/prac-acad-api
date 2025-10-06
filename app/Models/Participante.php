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
        'estado',
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
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'discapacidad'     => 'boolean',
        'repitente'        => 'boolean',
        'fechacreacion'    => 'datetime',
        'fechamodificacion'=> 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

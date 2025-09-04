<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fecha extends Model
{
    use HasFactory;
    
    protected $table = 'fechas';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'fecha_apertura_preg',
        'fecha_cierre_docente_preg',
        'fecha_cierre_jefe_depart',
        'fecha_cierre_decano',
        'fecha_apertura_postg',
        'fecha_cierre_docente_postg',
        'fecha_cierre_coordinador_postg',
        'fecha_cierre_jefe_postg',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_apertura_preg' => 'date',
        'fecha_cierre_docente_preg' => 'date',
        'fecha_cierre_jefe_depart' => 'date',
        'fecha_cierre_decano' => 'date',
        'fecha_apertura_postg' => 'date',
        'fecha_cierre_docente_postg' => 'date',
        'fecha_cierre_coordinador_postg' => 'date',
        'fechacreacion' => 'datetime',
        'fechamodificacion' => 'datetime',
    ];
}


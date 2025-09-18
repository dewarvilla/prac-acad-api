<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creacion extends Model
{
    use HasFactory;

    protected $table = 'creaciones';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'catalogo_id',  
        'nivel_academico',
        'facultad',
        'programa_academico',
        'nombre_practica',
        'recursos_necesarios',
        'justificacion',
        'estado_practica',
        'estado_depart',
        'estado_consejo_facultad',
        'estado_consejo_academico',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fechacreacion'     => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }
    
    public function programaciones()
    {
        return $this->hasMany(Programacion::class, 'creacion_id');
    }
}

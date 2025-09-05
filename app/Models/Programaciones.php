<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programacion extends Model
{
    use HasFactory;

    protected $table = 'programaciones';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'nombre',
        'nivel',
        'facultad',
        'programa_academico',
        'descripcion',
        'lugar_de_realizacion',
        'justificacion',
        'recursos_necesarios',
        'estado_practica',
        'estado_depart',
        'estado_postg',
        'estado_decano',
        'estado_jefe_postg',
        'estado_vice',
        'fecha_inicio',
        'fecha_finalizacion',
        'requiere_transporte',
        'creacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_inicio'        => 'date',
        'fecha_finalizacion'  => 'date',
        'requiere_transporte' => 'boolean',
        'fechacreacion'       => 'datetime',
        'fechamodificacion'   => 'datetime',
    ];

    public function creacion()
    {
        return $this->belongsTo(Creacion::class, 'creacion_id');
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'programacion_id');
    }

    public function auxilios()
    {
        return $this->hasMany(Auxilio::class, 'programacion_id');
    }

    public function rutas()
    {
        return $this->hasMany(Ruta::class, 'programacion_id');
    }

    public function reprogramaciones()
    {
        return $this->hasMany(Reprogramacion::class, 'programacion_id');
    }

    public function legalizaciones()
    {
        return $this->hasMany(Legalizacion::class, 'programacion_id');
    }

    public function ajustes()
    {
        return $this->hasMany(Ajuste::class, 'programacion_id');
    }
}

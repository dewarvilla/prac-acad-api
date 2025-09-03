<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Practica extends Model
{
    use HasFactory;

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
        'fecha_finalizacion',
        'fecha_solicitud',
        'user_id',
        'estado',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion'
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class);
    }

    public function auxilios()
    {
        return $this->hasMany(Auxilio::class);
    }

    public function rutas()
    {
        return $this->hasMany(Ruta::class);
    }

    public function reprogramaciones()
    {
        return $this->hasMany(Reprogramacion::class);
    }

    public function legalizaciones()
    {
        return $this->hasMany(Legalizacion::class);
    }
}

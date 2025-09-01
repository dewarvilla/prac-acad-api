<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_identificacion',
        'tipo_participante',
        'discapacidad',
        'nombre',
        'apellido',
        'correo_institucional',
        'telefono',
        'programa_academico',
        'facultad',
        'repitente',
        'practica_id',
        'user_id',
    ];

    // Relaciones
    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxilio extends Model
{
    use HasFactory;

    protected $fillable = [
        'pernocta',
        'distancias_mayor_70km',
        'fuera_cordoba',
        'numero_total_estudiantes',
        'numero_total_docentes',
        'numero_total_acompanantes',
        'valor_por_docente',
        'valor_por_estudiante',
        'valor_por_acompanante',
        'valor_total_auxilio',
        'practica_id',
        'estado',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion'
    ];

    public function practica()
    {
        return $this->belongsTo(Practica::class);
    }
}

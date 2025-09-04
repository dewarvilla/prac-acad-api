<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auxilio extends Model
{
    use HasFactory;

    protected $table = 'auxilios';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

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
        'programacion_id',
        'fechacreacion',
        'usuariocreacion',
        'fechamodificacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'pernocta' => 'boolean',
        'distancias_mayor_70km' => 'boolean',
        'fuera_cordoba' => 'boolean',
        'numero_total_estudiantes' => 'integer',
        'numero_total_docentes' => 'integer',
        'numero_total_acompanantes' => 'integer',
        'valor_por_docente' => 'decimal:2',
        'valor_por_estudiante' => 'decimal:2',
        'valor_por_acompanante' => 'decimal:2',
        'valor_total_auxilio' => 'decimal:2',
        'fechacreacion' => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    // Relaciones
    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}


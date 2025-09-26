<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auxilio extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'auxilios';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'estado',
        'pernocta',
        'distancias_mayor_70km',
        'fuera_cordoba',
        'valor_por_docente',
        'valor_por_estudiante',
        'valor_por_acompanante',
        'programacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'pernocta'                    => 'boolean',
        'distancias_mayor_70km'       => 'boolean',
        'fuera_cordoba'               => 'boolean',
        'valor_por_docente'           => 'decimal:2',
        'valor_por_estudiante'        => 'decimal:2',
        'valor_por_acompanante'       => 'decimal:2',
        'fechacreacion'               => 'datetime',
        'fechamodificacion'           => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

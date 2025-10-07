<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Fecha extends Model
{
    use HasFactory;

    protected $table = 'fechas';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'estado',
        'periodo',
        'fecha_apertura_preg',
        'fecha_cierre_docente_preg',
        'fecha_cierre_jefe_depart',
        'fecha_cierre_decano',
        'fecha_apertura_postg',
        'fecha_cierre_docente_postg',
        'fecha_cierre_coordinador_postg',
        'fecha_cierre_jefe_postg',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'periodo'                    => 'string',
        'fecha_apertura_preg'        => 'date',
        'fecha_cierre_docente_preg'  => 'date',
        'fecha_cierre_jefe_depart'   => 'date',
        'fecha_cierre_decano'        => 'date',
        'fecha_apertura_postg'       => 'date',
        'fecha_cierre_docente_postg' => 'date',
        'fecha_cierre_coordinador_postg' => 'date',
        'fecha_cierre_jefe_postg'    => 'date',
        'fechacreacion'              => 'datetime',
        'fechamodificacion'          => 'datetime',
    ];

    /** Ventana SOLO para programación por DOCENTE (no aprobaciones). */
    public function ventanaDocente(string $nivel): array
    {
        if ($nivel === 'pregrado') {
            return [$this->fecha_apertura_preg, $this->fecha_cierre_docente_preg];
        }
        if ($nivel === 'postgrado') {
            return [$this->fecha_apertura_postg, $this->fecha_cierre_docente_postg];
        }
        return [null, null];
    }

    /** Inclusivo: inicio <= fecha <= fin */
    public static function dentro(?Carbon $inicio, ?Carbon $fin, Carbon $fecha): bool
    {
        if (!$inicio || !$fin) return false;
        return $fecha->gte($inicio) && $fecha->lte($fin);
    }
}

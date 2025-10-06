<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    use HasFactory;

    protected $table = 'ajustes';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'fecha_ajuste',
        'estado_ajuste',
        'estado_vice',
        'estado_jefe_depart',
        'estado_coordinardor_postg',
        'justificacion',
        'programacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_ajuste'      => 'date',
        'fechacreacion'     => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

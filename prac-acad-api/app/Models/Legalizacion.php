<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legalizacion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'legalizaciones';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'estado_legalizacion',
        'fecha_legalizacion',
        'estado_depart',
        'estado_postg',
        'estado_logistica',
        'estado_tesoreria',
        'estado_contabilidad',
        'programacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_legalizacion' => 'date',
        'fechacreacion'      => 'datetime',
        'fechamodificacion'  => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

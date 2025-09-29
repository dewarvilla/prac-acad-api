<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reprogramacion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'reprogramaciones';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'fecha_reprogramacion',
        'estado_reprogramacion',
        'tipo_reprogramacion',
        'estado_vice',
        'justificacion',
        'programacion_id',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fecha_reprogramacion' => 'date',
        'fechacreacion'        => 'datetime',
        'fechamodificacion'    => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }
}

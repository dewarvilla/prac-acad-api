<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rutapeaje;

class Ruta extends Model
{
    use HasFactory;

    protected $table = 'rutas';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'programacion_id',

        'origen_lat','origen_lng','origen_desc','origen_place_id',
        'destino_lat','destino_lng','destino_desc','destino_place_id',

        'distancia_m','duracion_s','polyline',

        'numero_peajes','valor_peajes',
        'orden',
        'justificacion',

        'estado',
        'usuariocreacion','usuariomodificacion',
        'ipcreacion','ipmodificacion',
    ];

    protected $casts = [
        'origen_lat'   => 'float',
        'origen_lng'   => 'float',
        'destino_lat'  => 'float',
        'destino_lng'  => 'float',
        'distancia_m'  => 'integer',
        'duracion_s'   => 'integer',
        'numero_peajes'=> 'integer',
        'valor_peajes' => 'decimal:2',
        'orden'        => 'integer',
        'estado'       => 'boolean',
        'fechacreacion'=> 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }

    public function peajes()
    {
        return $this->hasMany(Rutapeaje::class, 'ruta_id');
    }
}
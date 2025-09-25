<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalogo extends Model
{
    use HasFactory;

    protected $table = 'catalogos';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'estado',
        'nivel_academico',
        'facultad',
        'programa_academico',
        'usuariocreacion',
        'usuariomodificacion',
        'ipcreacion',
        'ipmodificacion',
    ];

    protected $casts = [
        'fechacreacion'     => 'datetime',
        'fechamodificacion' => 'datetime',
    ];

    public function creaciones()
    {
        return $this->hasMany(Creacion::class, 'catalogo_id');
    }
}

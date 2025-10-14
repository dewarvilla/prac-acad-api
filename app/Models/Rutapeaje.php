<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rutapeaje extends Model
{
    protected $table = 'rutapeajes';

    const CREATED_AT = 'fechacreacion';
    const UPDATED_AT = 'fechamodificacion';

    protected $fillable = [
        'ruta_id','nombre','lat','lng','distancia_m','orden_km',
        'cat_i','cat_ii','cat_iii','cat_iv','cat_v','cat_vi','cat_vii',
        'fuente','fecha_tarifa',
        'fechacreacion','fechamodificacion'
    ];

    protected $casts = [
        'lat' => 'float', 'lng' => 'float',
        'distancia_m' => 'integer', 'orden_km' => 'decimal:2',
        'cat_i' => 'decimal:2','cat_ii' => 'decimal:2','cat_iii' => 'decimal:2',
        'cat_iv'=> 'decimal:2','cat_v'  => 'decimal:2','cat_vi'   => 'decimal:2','cat_vii'=> 'decimal:2',
        'fecha_tarifa' => 'date',
        'fechacreacion'=> 'datetime', 'fechamodificacion'=>'datetime',
    ];

    public function ruta()
    {
        return $this->belongsTo(Ruta::class, 'ruta_id');
    }
}

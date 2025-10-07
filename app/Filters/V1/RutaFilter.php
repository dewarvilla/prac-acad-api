<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class RutaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'             => ['eq','in','gt','lt','gte','lte'],

        'programacion_id'=> ['eq','in'],

        'origen_lat'     => ['eq','gt','gte','lt','lte','btn'],
        'origen_lng'     => ['eq','gt','gte','lt','lte','btn'],
        'destino_lat'    => ['eq','gt','gte','lt','lte','btn'],
        'destino_lng'    => ['eq','gt','gte','lt','lte','btn'],
        'origen_desc'    => ['eq','lk'],
        'destino_desc'   => ['eq','lk'],

        'distancia_m'    => ['eq','gt','gte','lt','lte','btn'],
        'duracion_s'     => ['eq','gt','gte','lt','lte','btn'],
        'numero_peajes'  => ['eq','gt','gte','lt','lte','btn'],
        'valor_peajes'   => ['eq','gt','gte','lt','lte','btn'],
        'orden'          => ['eq','gt','gte','lt','lte'],

        'estado'         => ['eq'],

        'fechacreacion'  => ['eq','gt','gte','lt','lte','btn'],
        'fechamodificacion' => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'   => ['eq','in'],
        'usuariomodificacion'=> ['eq','in'],
        'ipcreacion'        => ['eq'],
        'ipmodificacion'    => ['eq'],
    ];

    protected array $columnMap = [
        'programacionId' => 'programacion_id',

        'origenLat'      => 'origen_lat',
        'origenLng'      => 'origen_lng',
        'destinoLat'     => 'destino_lat',
        'destinoLng'     => 'destino_lng',
        'origenDesc'     => 'origen_desc',
        'destinoDesc'    => 'destino_desc',

        'distanciaM'     => 'distancia_m',
        'duracionS'      => 'duracion_s',
        'numeroPeajes'   => 'numero_peajes',
        'valorPeajes'    => 'valor_peajes',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

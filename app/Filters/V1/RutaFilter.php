<?php

namespace App\Filters\V1;

class RutaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                    => ['eq','in','gt','lt','gte','lte'],
        'latitud_salidas'       => ['eq','lk'],
        'latitud_llegadas'      => ['eq','lk'],
        'numero_recorridos'     => ['eq','gt','gte','lt','lte','btn'],
        'numero_peajes'         => ['eq','gt','gte','lt','lte','btn'],
        'valor_peajes'          => ['eq','gt','gte','lt','lte','btn'],
        'valor_total_peajes'    => ['eq','gt','gte','lt','lte','btn'],
        'distancia_trayectos_km'=> ['eq','gt','gte','lt','lte','btn'],
        'distancia_total_km'    => ['eq','gt','gte','lt','lte','btn'],
        'ruta_salida'           => ['eq','lk'],
        'ruta_llegada'          => ['eq','lk'],

        'programacion_id'       => ['eq','in'],

        'fechacreacion'         => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'       => ['eq','in'],
        'fechamodificacion'     => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'   => ['eq','in'],
        'ipcreacion'            => ['eq'],
        'ipmodificacion'        => ['eq'],
    ];

    protected array $columnMap = [
        'latitudSalidas'        => 'latitud_salidas',
        'latitudLlegadas'       => 'latitud_llegadas',
        'numeroRecorridos'      => 'numero_recorridos',
        'numeroPeajes'          => 'numero_peajes',
        'valorPeajes'           => 'valor_peajes',
        'valorTotalPeajes'      => 'valor_total_peajes',
        'distanciaTrayectosKm'  => 'distancia_trayectos_km',
        'distanciaTotalKm'      => 'distancia_total_km',
        'rutaSalida'            => 'ruta_salida',
        'rutaLlegada'           => 'ruta_llegada',
        'programacionId'        => 'programacion_id',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

<?php

namespace App\Filters\V1;

class RutaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'origen_lat' => ['eq','gt','gte','lt','lte','btn'],
        'origen_lng' => ['eq','gt','gte','lt','lte','btn'],
        'destino_lat' => ['eq','gt','gte','lt','lte','btn'],
        'destino_lng' => ['eq','gt','gte','lt','lte','btn'],

        'numero_recorridos' => ['eq','gt','gte','lt','lte','btn'],
        'numero_peajes' => ['eq','gt','gte','lt','lte','btn'],

        'valor_peajes' => ['eq','gt','gte','lt','lte','btn'],
        'valor_total_peajes' => ['eq','gt','gte','lt','lte','btn'],
        'distancia_trayectos_km' => ['eq','gt','gte','lt','lte','btn'],
        'distancia_total_km' => ['eq','gt','gte','lt','lte','btn'],

        'ruta_salida' => ['eq','lk'],
        'ruta_llegada' => ['eq','lk'],

        'practica_id' => ['eq','in'],
        'created_at' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [
        'origenLat' => 'origen_lat',
        'origenLng' => 'origen_lng',
        'destinoLat' => 'destino_lat',
        'destinoLng' => 'destino_lng',

        'numeroRecorridos' => 'numero_recorridos',
        'numeroPeajes' => 'numero_peajes',

        'valorPeajes' => 'valor_peajes',
        'valorTotalPeajes' => 'valor_total_peajes',
        'distanciaTrayectosKm' => 'distancia_trayectos_km',
        'distanciaTotalKm' => 'distancia_total_km',

        'rutaSalida' => 'ruta_salida',
        'rutaLlegada' => 'ruta_llegada',

        'practicaId' => 'practica_id',
        'createdAt' => 'created_at',
    ];
    protected array $dateFilters = ['created_at'];
}

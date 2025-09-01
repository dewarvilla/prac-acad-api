<?php

namespace App\Filters\V1;

class LegalizacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],

        'fecha_legalizacion' => ['eq','gt','gte','lt','lte','btn'],

        'estado_depart' => ['eq','in'],
        'estado_postg' => ['eq','in'],
        'estado_tesoreria' => ['eq','in'],
        'estado_contabilidad' => ['eq','in'],
        
        'practica_id' => ['eq','in'],
    ];

    protected array $columnMap = [
        'fechaLegalizacion' => 'fecha_legalizacion',

        'estadoDepart' => 'estado_depart',
        'estadoPostg' => 'estado_postg',
        'estadoTesoreria' => 'estado_tesoreria',
        'estadoContabilidad' => 'estado_contabilidad',

        'practicaId' => 'practica_id',
    ];
    protected array $dateFilters = ['fecha_legalizacion'];
}

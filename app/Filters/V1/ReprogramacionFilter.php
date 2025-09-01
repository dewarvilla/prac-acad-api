<?php

namespace App\Filters\V1;

class ReprogramacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'fecha_reprogramacion' => ['eq','gt','gte','lt','lte','btn'],
        'estado_reprogramacion' => ['eq','in'],
        'tipo_reprogramacion' => ['eq','in'],
        'estado_vice' => ['eq','in'],
        'justificacion' => ['eq','lk'],
        'practica_id' => ['eq','in'],
    ];

    protected array $columnMap = [
        'fechaReprogramacion' => 'fecha_reprogramacion',
        'estadoReprogramacion' => 'estado_reprogramacion',
        'tipoReprogramacion' => 'tipo_reprogramacion',
        'estadoVice' => 'estado_vice',
        'practicaId' => 'practica_id',
    ];
    protected array $dateFilters = ['fecha_reprogramacion'];
}

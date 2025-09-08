<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ReprogramacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                   => ['eq','in','gt','lt','gte','lte'],
        'fecha_reprogramacion' => ['eq','gt','gte','lt','lte','btn'],
        'estado_reprogramacion'=> ['eq','in'],
        'tipo_reprogramacion'  => ['eq','in'],
        'estado_vice'          => ['eq','in'],
        'justificacion'        => ['eq','lk'],

        'programacion_id'      => ['eq','in'],

        'fechacreacion'        => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'      => ['eq','in'],
        'fechamodificacion'    => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'  => ['eq','in'],
        'ipcreacion'           => ['eq'],
        'ipmodificacion'       => ['eq'],
    ];

    protected array $columnMap = [
        'fechaReprogramacion'  => 'fecha_reprogramacion',
        'estadoReprogramacion' => 'estado_reprogramacion',
        'tipoReprogramacion'   => 'tipo_reprogramacion',
        'estadoVice'           => 'estado_vice',
        'programacionId'       => 'programacion_id',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion','fecha_reprogramacion'];
}

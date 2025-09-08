<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class LegalizacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                  => ['eq','in','gt','lt','gte','lte'],
        'fecha_legalizacion'  => ['eq','gt','gte','lt','lte','btn'],

        'estado_depart'       => ['eq','in'],
        'estado_postg'        => ['eq','in'],
        'estado_logistica'    => ['eq','in'],
        'estado_tesoreria'    => ['eq','in'],
        'estado_contabilidad' => ['eq','in'],

        'programacion_id'     => ['eq','in'],

        'fechacreacion'       => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'     => ['eq','in'],
        'fechamodificacion'   => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion' => ['eq','in'],
        'ipcreacion'          => ['eq'],
        'ipmodificacion'      => ['eq'],
    ];

    protected array $columnMap = [
        'fechaLegalizacion'   => 'fecha_legalizacion',
        'estadoDepart'        => 'estado_depart',
        'estadoPostg'         => 'estado_postg',
        'estadoLogistica'     => 'estado_logistica',
        'estadoTesoreria'     => 'estado_tesoreria',
        'estadoContabilidad'  => 'estado_contabilidad',
        'programacionId'      => 'programacion_id',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion','fecha_legalizacion'];
}

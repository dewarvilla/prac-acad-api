<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class AjusteFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                   => ['eq','in','gt','lt','gte','lte'],
        'fecha_ajuste'         => ['eq','gt','gte','lt','lte','btn'],
        'estado_ajuste' => ['eq','in'], 
        'estado_vice'   => ['eq','in'],    
        'estado_jefe_depart' => ['eq','in'],  
        'estado_coordinador_postg'  => ['eq','in'],    
        'justificacion' => ['eq','lk'],
        'programacion_id'      => ['eq','in'],

        'fechacreacion'        => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'      => ['eq','in'],
        'fechamodificacion'    => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'  => ['eq','in'],
        'ipcreacion'           => ['eq'],
        'ipmodificacion'       => ['eq'],
    ];

    protected array $columnMap = [
        'fechaAjuste'          => 'fecha_ajuste',
        'estadoAjuste'         => 'estado_ajuste',
        'estadoJefeDepart'    => 'estado_jefe_depart',
        'estadoCoordinadorPostg' => 'estado_coordinardo_postg',
        'estadoVice'           => 'estado_vice',
        'programacionId'       => 'programacion_id',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion', 'fecha_ajuste'];
}
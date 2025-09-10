<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class FechaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                          => ['eq','in','gt','lt','gte','lte'],
        'periodo'                     => ['eq','in','like'],
        'fecha_apertura_preg'         => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_docente_preg'   => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_jefe_depart'    => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_decano'         => ['eq','gt','gte','lt','lte','btn'],
        'fecha_apertura_postg'        => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_docente_postg'  => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_coordinador_postg' => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre_jefe_postg'     => ['eq','gt','gte','lt','lte','btn'],

        'fechacreacion'               => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'             => ['eq','in'],
        'fechamodificacion'           => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'         => ['eq','in'],
        'ipcreacion'                  => ['eq'],
        'ipmodificacion'              => ['eq'],
    ];

    protected array $columnMap = [
        'fechaAperturaPreg'           => 'fecha_apertura_preg',
        'fechaCierreDocentePreg'      => 'fecha_cierre_docente_preg',
        'fechaCierreJefeDepart'       => 'fecha_cierre_jefe_depart',
        'fechaCierreDecano'           => 'fecha_cierre_decano',
        'fechaAperturaPostg'          => 'fecha_apertura_postg',
        'fechaCierreDocentePostg'     => 'fecha_cierre_docente_postg',
        'fechaCierreCoordinadorPostg' => 'fecha_cierre_coordinador_postg',
        'fechaCierreJefePostg'        => 'fecha_cierre_jefe_postg',
    ];

    protected array $dateFilters = [
        'fechacreacion','fechamodificacion',
        'fecha_apertura_preg','fecha_cierre_docente_preg','fecha_cierre_jefe_depart','fecha_cierre_decano',
        'fecha_apertura_postg','fecha_cierre_docente_postg','fecha_cierre_coordinador_postg', 'fecha_cierre_jefe_postg',
    ];
}

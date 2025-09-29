<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CatalogoFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'nivel_academico' => ['eq','in'],
        'facultad' => ['eq','lk'],
        'programa_academico' => ['eq','lk'],

        'fechacreacion'          => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'        => ['eq','in'],
        'fechamodificacion'      => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'    => ['eq','in'],
        'ipcreacion'             => ['eq'],
        'ipmodificacion'         => ['eq'],
    ];

    protected array $columnMap = [
        'nivelAcademico' => 'nivel_academico',
        'programaAcademico' => 'programa_academico',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

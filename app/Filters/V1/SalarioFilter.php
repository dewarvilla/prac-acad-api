<?php

namespace App\Filters\V1;

class SalarioFilter extends ApiFilter
{
    protected array $safeParms = [
        'salario_id'    => ['eq','in','gt','lt','gte','lte'],
        'anio'  => ['eq','gt','gte','lt','lte','in','btn'],
        'valor' => ['eq','gt','gte','lt','lte','btn'],

        'fechacreacion'     => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'   => ['eq','in'],
        'fechamodificacion' => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'=> ['eq','in'],
        'ipcreacion'        => ['eq'],
        'ipmodificacion'    => ['eq'],
    ];

    protected array $columnMap = [
        'salarioId'     => 'salario_id',
    ];

    protected array $dateFilters = [
        'fechacreacion','fechamodificacion',
    ];
}

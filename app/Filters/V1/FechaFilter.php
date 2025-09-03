<?php

namespace App\Filters\V1;

class FechaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'fecha_apertura' => ['eq','gt','gte','lt','lte','btn'],
        'fecha_cierre' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [
        'fechaApertura' => 'fecha_apertura',
        'fechaCierre' => 'fecha_cierre',
    ];
    protected array $dateFilters = ['fecha_apertura', 'fecha_cierre']; 
}

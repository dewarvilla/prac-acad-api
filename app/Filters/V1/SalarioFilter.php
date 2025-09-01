<?php

namespace App\Filters\V1;

class SalarioFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'anio' => ['eq','gt','gte','lt','lte','in','btn'],
        'valor' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [];
    protected array $dateFilters = []; // sin fechas
}

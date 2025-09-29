<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class AuxilioFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                         => ['eq','in','gt','lt','gte','lte'],
        'pernocta'                   => ['eq'],
        'distancias_mayor_70km'      => ['eq'],
        'fuera_cordoba'              => ['eq'],

        'valor_por_docente'          => ['eq','gt','gte','lt','lte','btn'],
        'valor_por_estudiante'       => ['eq','gt','gte','lt','lte','btn'],
        'valor_por_acompanante'      => ['eq','gt','gte','lt','lte','btn'],

        'programacion_id'            => ['eq','in'],

        'fechacreacion'              => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'            => ['eq','in'],
        'fechamodificacion'          => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'        => ['eq','in'],
        'ipcreacion'                 => ['eq'],
        'ipmodificacion'             => ['eq'],
    ];

    protected array $columnMap = [
        'distanciasMayor70km'     => 'distancias_mayor_70km',
        'fueraCordoba'            => 'fuera_cordoba',
        'valorPorDocente'         => 'valor_por_docente',
        'valorPorEstudiante'      => 'valor_por_estudiante',
        'valorPorAcompanante'     => 'valor_por_acompanante',
        'programacionId'          => 'programacion_id',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

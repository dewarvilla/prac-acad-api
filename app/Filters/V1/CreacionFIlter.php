<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class CreacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'nombre_practica' => ['eq','lk'],
        'recursos_necesarios' => ['eq','lk'],
        'justificacion' => ['eq','lk'],
        'estado_practica' => ['eq','in'],
        'estado_depart' => ['eq','in'],
        'estado_consejo_facultad'=> ['eq','in'],
        'estado_consejo_academico'=> ['eq','in'],
        'catalogo_id' =>['eq', 'in'],

        'fechacreacion'          => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'        => ['eq','in'],
        'fechamodificacion'      => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'    => ['eq','in'],
        'ipcreacion'             => ['eq'],
        'ipmodificacion'         => ['eq'],
    ];

    protected array $columnMap = [
        'catalogoId' => 'catalogo_id',
        'nombrePractica' => 'nombre_practica',
        'recursosNecesarios' => 'recursos_necesarios',
        'estadoPractica' => 'estado_practica',
        'estadoDepart' => 'estado_depart',
        'estadoConsejoFacultad' => 'estado_consejo_facultad',
        'estadoConsejoAcademico' => 'estado_consejo_academico',
        'justificacion' => 'justificacion',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

<?php

namespace App\Filters\V1;

class CreacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'creacion_id' => ['eq','in','gt','lt','gte','lte'],
        'nivel_academico' => ['eq','in'],
        'facultad' => ['eq','lk'],
        'programa_academico' => ['eq','lk'],
        'nombre_practica' => ['eq','lk'],
        'recursos_necesarios' => ['eq','lk'],
        'jutificacion' => ['eq','lk'],
        'estado_practica' => ['eq','in'],
        'estado_depart' => ['eq','in'],
        'estado_consejo_facultad'=> ['eq','in'],
        'estado_consejo_academico'=> ['eq','in'],

        'fechacreacion'          => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'        => ['eq','in'],
        'fechamodificacion'      => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'    => ['eq','in'],
        'ipcreacion'             => ['eq'],
        'ipmodificacion'         => ['eq'],
    ];

    protected array $columnMap = [
        'creacionId' => 'creacion_id',
        'nivelAcademico' => 'nivel_academico',
        'programaAcademico' => 'programa_academico',
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

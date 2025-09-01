<?php

namespace App\Filters\V1;

class PracticaFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'nombre' => ['eq','lk'],
        'nivel' => ['eq','in'],
        'facultad' => ['eq','lk'],
        'programa_academico' => ['eq','lk'],
        'descripcion' => ['lk','eq'],
        'lugar_de_realizacion' => ['lk','eq'],

        'estado_practica' => ['eq','in'],
        'estado_depart' => ['eq','in'],
        'estado_postg' => ['eq','in'],
        'estado_decano' => ['eq','in'],
        'estado_jefe_postg' => ['eq','in'],
        'estado_vice' => ['eq','in'],

        'user_id' => ['eq','in'],

        'fecha_solicitud' => ['eq','gt','gte','lt','lte','btn'],
        'fecha_finalizacion' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [
        'programaAcademico' => 'programa_academico',

        'estadoPractica' => 'estado_practica',
        'estadoDepart' => 'estado_depart',
        'estadoPostg' => 'estado_postg',
        'estadoDecano' => 'estado_decano',
        'estadoJefePostg' => 'estado_jefe_postg',
        'estadoVice' => 'estado_vice',

        'userId' => 'user_id',

        'fechaSolicitud' => 'fecha_solicitud',
        'fechaFinalizacion' => 'fecha_finalizacion',
    ];

    protected array $dateFilters = ['fecha_solicitud','fecha_finalizacion'];
}


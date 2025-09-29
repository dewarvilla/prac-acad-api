<?php

namespace App\Filters\V1;

use App\Filters\ApiFilter;

class ProgramacionFilter extends ApiFilter
{
    protected array $safeParms = [
        'id'                   => ['eq','in','gt','lt','gte','lte'],
        'nombre_practica'      => ['eq','lk'],
        'descripcion'          => ['eq','lk'],
        'lugar_de_realizacion' => ['eq','lk'],
        'justificacion'        => ['eq','lk'],
        'recursos_necesarios'  => ['eq','lk'],

        'estado_practica'      => ['eq','in'],
        'estado_depart'        => ['eq','in'],
        'estado_postg'         => ['eq','in'],
        'estado_decano'        => ['eq','in'],
        'estado_jefe_postg'    => ['eq','in'],
        'estado_vice'          => ['eq','in'],

        'fecha_inicio'         => ['eq','gt','gte','lt','lte','btn'],
        'fecha_finalizacion'   => ['eq','gt','gte','lt','lte','btn'],
        'creacion_id'          => ['eq','in'],

        'fechacreacion'        => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'      => ['eq','in'],
        'fechamodificacion'    => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'  => ['eq','in'],
        'ipcreacion'           => ['eq'],
        'ipmodificacion'       => ['eq'],
        'requiere_transporte'  => ['eq'],
    ];

    protected array $columnMap = [
        'nombrePractica'     => 'nombre_practica',
        'lugarDeRealizacion' => 'lugar_de_realizacion',
        'recursosNecesarios' => 'recursos_necesarios',
        'estadoPractica'     => 'estado_practica',
        'estadoDepart'       => 'estado_depart',
        'estadoPostg'        => 'estado_postg',
        'estadoDecano'       => 'estado_decano',
        'estadoJefePostg'    => 'estado_jefe_postg',
        'estadoVice'         => 'estado_vice',
        'fechaInicio'        => 'fecha_inicio',
        'fechaFinalizacion'  => 'fecha_finalizacion',
        'creacionId'         => 'creacion_id',
        'fechacreacion'      => 'fechacreacion',
        'fechamodificacion'  => 'fechamodificacion',
    ];

    protected array $dateFilters = [
        'fechacreacion','fechamodificacion','fecha_inicio','fecha_finalizacion',
    ];
}

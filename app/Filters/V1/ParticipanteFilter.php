<?php

namespace App\Filters\V1;

class ParticipanteFilter extends ApiFilter
{
    protected array $safeParms = [
        'participante_id'                    => ['eq','in','gt','lt','gte','lte'],
        'numero_identificacion' => ['eq','lk'],
        'tipo_participante'     => ['eq','in'], // 'estudiante','docente','acompanante'
        'discapacidad'          => ['eq'],
        'nombre'                => ['eq','lk'],
        'correo_institucional'  => ['eq','lk'],
        'telefono'              => ['eq','lk'],
        'programa_academico'    => ['eq','lk'],
        'facultad'              => ['eq','lk'],
        'repitente'             => ['eq'],

        'programacion_id'       => ['eq','in'],

        'fechacreacion'         => ['eq','gt','gte','lt','lte','btn'],
        'usuariocreacion'       => ['eq','in'],
        'fechamodificacion'     => ['eq','gt','gte','lt','lte','btn'],
        'usuariomodificacion'   => ['eq','in'],
        'ipcreacion'            => ['eq'],
        'ipmodificacion'        => ['eq'],
    ];

    protected array $columnMap = [
        'participanteId'        => 'participante_id',
        'numeroIdentificacion'  => 'numero_identificacion',
        'tipoParticipante'      => 'tipo_participante',
        'correoInstitucional'   => 'correo_institucional',
        'programaAcademico'     => 'programa_academico',
        'programacionId'        => 'programacion_id',
        'createdAt'             => 'fechacreacion',
        'updatedAt'             => 'fechamodificacion',
    ];

    protected array $dateFilters = ['fechacreacion','fechamodificacion'];
}

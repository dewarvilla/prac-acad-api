<?php

namespace App\Filters\V1;

class ParticipanteFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'numero_identificacion' => ['eq','lk'],
        'tipo_participante' => ['eq','in'],
        'discapacidad' => ['eq'],
        'nombre' => ['eq','lk'],
        'apellido' => ['eq','lk'],
        'correo_institucional' => ['eq','lk'],
        'telefono' => ['eq','lk'],
        'programa_academico' => ['eq','lk'],
        'facultad' => ['eq','lk'],
        'repitente' => ['eq'],
        'practica_id' => ['eq','in'],
        'user_id' => ['eq','in'],
        'created_at' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [
        'numeroIdentificacion' => 'numero_identificacion',
        'tipoParticipante' => 'tipo_participante',

        'correoInstitucional' => 'correo_institucional',
        'programaAcademico' => 'programa_academico',

        'practicaId' => 'practica_id',
        'userId' => 'user_id',
        'createdAt' => 'created_at',
    ];
    protected array $dateFilters = ['created_at'];
}

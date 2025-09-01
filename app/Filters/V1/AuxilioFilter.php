<?php

namespace App\Filters\V1;

class AuxilioFilter extends ApiFilter
{
    protected array $safeParms = [
        'id' => ['eq','in','gt','lt','gte','lte'],
        'pernocta' => ['eq'],
        'distancias_mayor_70km' => ['eq'],
        'fuera_cordoba' => ['eq'],

        'numero_total_estudiantes' => ['eq','gt','gte','lt','lte','btn'],
        'numero_total_docentes' => ['eq','gt','gte','lt','lte','btn'],
        'numero_total_acompanantes' => ['eq','gt','gte','lt','lte','btn'],

        'valor_por_docente' => ['eq','gt','gte','lt','lte','btn'],
        'valor_por_estudiante' => ['eq','gt','gte','lt','lte','btn'],
        'valor_por_acompanante' => ['eq','gt','gte','lt','lte','btn'],
        'valor_total_auxilio' => ['eq','gt','gte','lt','lte','btn'],

        'practica_id' => ['eq','in'],
        'created_at' => ['eq','gt','gte','lt','lte','btn'],
    ];

    protected array $columnMap = [
        'distanciasMayor70km' => 'distancias_mayor_70km',
        'fueraCordoba' => 'fuera_cordoba',

        'numeroTotalEstudiantes' => 'numero_total_estudiantes',
        'numeroTotalDocentes' => 'numero_total_docentes',
        'numeroTotalAcompanantes' => 'numero_total_acompanantes',

        'valorPorDocente' => 'valor_por_docente',
        'valorPorEstudiante' => 'valor_por_estudiante',
        'valorPorAcompanante' => 'valor_por_acompanante',
        'valorTotalAuxilio' => 'valor_total_auxilio',

        'practicaId' => 'practica_id',
        'createdAt' => 'created_at',
    ];
    protected array $dateFilters = ['created_at'];
}

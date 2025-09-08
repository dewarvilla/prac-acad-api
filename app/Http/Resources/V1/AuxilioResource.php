<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuxilioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'pernocta'                  => $this->pernocta,
            'distanciasMayor70km'       => $this->distancias_mayor_70km,
            'fueraCordoba'              => $this->fuera_cordoba,
            'numeroTotalEstudiantes'    => $this->numero_total_estudiantes,
            'numeroTotalDocentes'       => $this->numero_total_docentes,
            'numeroTotalAcompanantes'   => $this->numero_total_acompanantes,
            'valorPorDocente'           => $this->valor_por_docente,
            'valorPorEstudiante'        => $this->valor_por_estudiante,
            'valorPorAcompanante'       => $this->valor_por_acompanante,
            'valorTotalAuxilio'         => $this->valor_total_auxilio,
            'programacionId'            => $this->programacion_id,
            'fechacreacion'             => $this->fechacreacion,
            'usuariocreacion'           => $this->usuariocreacion,
            'fechamodificacion'         => $this->fechamodificacion,
            'usuariomodificacion'       => $this->usuariomodificacion,
            'ipcreacion'                => $this->ipcreacion,
            'ipmodificacion'            => $this->ipmodificacion,
        ];
    }
}

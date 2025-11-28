<?php

namespace App\Policies;

use App\Models\Programacion;
use App\Models\User;

class ProgramacionPolicy
{
    public function view(User $user, Programacion $programacion): bool
    {
        // Si no tiene permiso base, no ve nada
        if (! $user->can('programaciones.view')) {
            return false;
        }

        // Admin / super_admin ven siempre
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return true;
        }

        // Docente creador: ve SIEMPRE, sin importar etapa/estado
        if ((int) $programacion->usuariocreacion === (int) $user->id) {
            return true;
        }

        // A partir de aquí es para los "aprobadores"
        $nivel = strtolower($programacion->creacion->nivel_academico ?? '');
        $rechazada = $programacion->estado_practica === 'rechazada';

        // Nunca mostramos a aprobadores si está rechazada
        if ($rechazada) {
            return false;
        }

        // === FLUJO PREGRADO ===
        if ($nivel === 'pregrado') {
            // Jefe de departamento: etapa departamento pendiente
            if ($user->can('programaciones.aprobar.departamento')) {
                return $programacion->estado_depart === 'pendiente';
            }

            // Decano: departamento aprobada y decano pendiente
            if ($user->can('programaciones.aprobar.decano')) {
                return $programacion->estado_depart === 'aprobada'
                    && $programacion->estado_decano === 'pendiente';
            }

            // Vicerrectoría: decano aprobada y vice pendiente
            if ($user->can('programaciones.aprobar.vicerrectoria')) {
                return $programacion->estado_decano === 'aprobada'
                    && $programacion->estado_vice === 'pendiente';
            }
        }

        // === FLUJO POSTGRADO ===
        if ($nivel === 'postgrado') {
            // Responsable de postgrados: etapa postg pendiente
            if ($user->can('programaciones.aprobar.postgrados')) {
                return $programacion->estado_postg === 'pendiente';
            }

            // Jefe de postgrados: postg aprobada y jefe_postg pendiente
            if ($user->can('programaciones.aprobar.jefe_postgrados')) {
                return $programacion->estado_postg === 'aprobada'
                    && $programacion->estado_jefe_postg === 'pendiente';
            }

            // Vicerrectoría: jefe_postg aprobada y vice pendiente
            if ($user->can('programaciones.aprobar.vicerrectoria')) {
                return $programacion->estado_jefe_postg === 'aprobada'
                    && $programacion->estado_vice === 'pendiente';
            }
        }

        // Si no cae en ningún caso, no la ve
        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('programaciones.create');
    }

    public function update(User $user, Programacion $programacion): bool
    {
        if (! $user->can('programaciones.edit')) {
            return false;
        }

        // Admin / super_admin pueden editar siempre
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return true;
        }

        // Solo el creador puede modificar (docente, etc.)
        if ((int) $programacion->usuariocreacion !== (int) $user->id) {
            return false;
        }

        // Si la práctica está rechazada, dejamos editar para corregir y reenviar
        if ($programacion->estado_practica === 'rechazada') {
            return true;
        }

        // Si ya pasó la primera instancia, no se puede modificar
        $nivel = strtolower($programacion->creacion->nivel_academico ?? '');

        if ($nivel === 'postgrado') {
            // primera instancia = postgrados
            if ($programacion->estado_postg === 'aprobada') {
                return false;
            }
        } else {
            // pregrado (departamento)
            if ($programacion->estado_depart === 'aprobada') {
                return false;
            }
        }

        return true;
    }

    public function delete(User $user, Programacion $programacion): bool
    {
        if (! $user->can('programaciones.delete')) {
            return false;
        }

        // Admin / super_admin pueden borrar siempre
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return true;
        }

        // Solo el creador puede borrar
        if ((int) $programacion->usuariocreacion !== (int) $user->id) {
            return false;
        }

        // Si la práctica está rechazada, permitimos eliminar
        if ($programacion->estado_practica === 'rechazada') {
            return true;
        }

        // Si ya pasó la primera instancia, no se puede eliminar
        $nivel = strtolower($programacion->creacion->nivel_academico ?? '');

        if ($nivel === 'postgrado') {
            if ($programacion->estado_postg === 'aprobada') {
                return false;
            }
        } else {
            if ($programacion->estado_depart === 'aprobada') {
                return false;
            }
        }

        return true;
    }
}

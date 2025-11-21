<?php

namespace App\Policies;

use App\Models\Programacion;
use App\Models\User;

class ProgramacionPolicy
{
    public function view(User $user, Programacion $programacion): bool
    {
        return $user->can('programaciones.view');
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

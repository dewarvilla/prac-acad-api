<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché de permisos (evita “seeding” con cache viejo)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ----- Permisos -----
        $perms = [
            // Ver
            'programaciones.ver',

            // Aprobaciones/Rechazos por etapa (Programaciones)
            'programaciones.aprobar.departamento',   'programaciones.rechazar.departamento',
            'programaciones.aprobar.postgrados',     'programaciones.rechazar.postgrados',
            'programaciones.aprobar.decano',         'programaciones.rechazar.decano',
            'programaciones.aprobar.jefe_postgrados','programaciones.rechazar.jefe_postgrados',
            'programaciones.aprobar.vicerrectoria',  'programaciones.rechazar.vicerrectoria',

            // (Etapa de Creación – HU-03, HU-04, HU-05)
            'creaciones.aprobar.comite_acreditacion',   'creaciones.rechazar.comite_acreditacion',
            'creaciones.aprobar.consejo_facultad',      'creaciones.rechazar.consejo_facultad',
            'creaciones.aprobar.consejo_academico',     'creaciones.rechazar.consejo_academico',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p], ['guard_name' => 'web']);
        }

        // ----- Roles -----
        $roles = [
            'docente' => [
                'programaciones.ver',
            ],
            'jefe_departamento' => [
                'programaciones.ver',
                'programaciones.aprobar.departamento',
                'programaciones.rechazar.departamento',
            ],
            'coordinador_postgrados' => [
                'programaciones.ver',
                'programaciones.aprobar.postgrados',
                'programaciones.rechazar.postgrados',
            ],
            'decano' => [
                'programaciones.ver',
                'programaciones.aprobar.decano',
                'programaciones.rechazar.decano',
            ],
            'jefe_postgrados' => [
                'programaciones.ver',
                'programaciones.aprobar.jefe_postgrados',
                'programaciones.rechazar.jefe_postgrados',
            ],
            'vicerrectoria' => [
                'programaciones.ver',
                'programaciones.aprobar.vicerrectoria',
                'programaciones.rechazar.vicerrectoria',
            ],

            // Etapa de “Creaciones” (HU-03..HU-05)
            'comite_acreditacion' => [
                'creaciones.aprobar.comite_acreditacion',
                'creaciones.rechazar.comite_acreditacion',
            ],
            'consejo_facultad' => [
                'creaciones.aprobar.consejo_facultad',
                'creaciones.rechazar.consejo_facultad',
            ],
            'consejo_academico' => [
                'creaciones.aprobar.consejo_academico',
                'creaciones.rechazar.consejo_academico',
            ],
        ];

        $super = Role::firstOrCreate(['name' => 'super_admin'], ['guard_name' => 'web']);
        $super->syncPermissions(Permission::all());

        foreach ($roles as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);
            $role->syncPermissions($rolePerms);
        }

        // Limpia y recarga cache de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}

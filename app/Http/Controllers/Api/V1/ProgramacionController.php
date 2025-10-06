<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Programacion;
use App\Filters\V1\ProgramacionFilter;
use App\Http\Resources\V1\ProgramacionResource;
use App\Http\Resources\V1\ProgramacionCollection;
use App\Http\Requests\V1\IndexProgramacionRequest;
use App\Http\Requests\V1\StoreProgramacionRequest;
use App\Http\Requests\V1\UpdateProgramacionRequest;
use App\Http\Requests\V1\BulkDeleteProgramacionRequest;
use Illuminate\Support\Facades\DB;

class ProgramacionController extends Controller
{
    public function index(IndexProgramacionRequest $request, ProgramacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Programacion::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('nombre_practica', $op, $like)
                  ->orWhere('lugar_de_realizacion', $op, $like)
                  ->orWhere('estado_practica', $op, $like)
                  ->orWhere('estado_depart', $op, $like)
                  ->orWhere('estado_postg', $op, $like)
                  ->orWhere('estado_decano', $op, $like)
                  ->orWhere('estado_jefe_postg', $op, $like)
                  ->orWhere('estado_vice', $op, $like)
                  ->orWhere('fecha_inicio', $op, $like)
                  ->orWhere('fecha_finalizacion', $op, $like);

                // match boolean requiere_transporte
                $low = strtolower($term);
                if (in_array($low, ['si','sí','true','1','no','false','0'], true)) {
                    $val = in_array($low, ['si','sí','true','1'], true) ? 1 : 0;
                    $qq->orWhere('requiere_transporte', $val);
                }

                if (ctype_digit($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        return $perPage > 0
            ? new ProgramacionCollection($q->paginate($perPage)->appends($request->query()))
            : ProgramacionResource::collection($q->get());
    }

    public function store(StoreProgramacionRequest $request)
    {
        $creacion = \App\Models\Creacion::findOrFail($request->input('creacion_id'));

        $now = now();
        $data = $request->validated();

        // Fuente única de la verdad
        $data['nombre_practica'] = $creacion->nombre_practica;

        $data += [
            'fechacreacion'       => $now,
            'fechamodificacion'   => $now,
            'usuariocreacion'     => auth()->id() ?? 0,
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipcreacion'          => $request->ip(),
            'ipmodificacion'      => $request->ip(),
        ];

        $programacion = Programacion::create($data);

        return (new ProgramacionResource($programacion))
            ->response()->setStatusCode(201);
    }

    public function show(Programacion $programacion)
    {
        return new ProgramacionResource($programacion);
    }

    public function update(UpdateProgramacionRequest $request, Programacion $programacion)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $programacion->update($data);

        return new ProgramacionResource($programacion->refresh());
    }

    public function destroy(Programacion $programacion)
    {
        $programacion->delete(); 
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteProgramacionRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Programacion::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Programaciones eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

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
use Illuminate\Database\QueryException;

class ProgramacionController extends Controller
{
    public function index(IndexProgramacionRequest $request, ProgramacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Programacion::query()->orderBy('id');
        $filter->apply($request, $q);

        return new ProgramacionCollection(
            $q->paginate($perPage)->appends($request->query())
        );
    }

    public function store(StoreProgramacionRequest $request)
    {
        $now = now();
        $data = $request->validated() + [
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
        try {
            $programacion->delete();
            return response()->noContent();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'No se puede eliminar: existen registros relacionados.'
                ], 409);
            }
            throw $e;
        }
    }
}


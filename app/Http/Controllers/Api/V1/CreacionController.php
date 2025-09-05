<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Creacion;
use App\Filters\V1\CreacionFilter;
use App\Http\Resources\V1\CreacionResource;
use App\Http\Resources\V1\CreacionCollection;
use App\Http\Requests\V1\IndexCreacionRequest;
use App\Http\Requests\V1\StoreCreacionRequest;
use App\Http\Requests\V1\UpdateCreacionRequest;
use Illuminate\Database\QueryException;

class CreacionController extends Controller
{
    public function index(IndexCreacionRequest $request, CreacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Creacion::query();
        $filter->apply($request, $q);

        return new CreacionCollection(
            $q->paginate($perPage)->appends($request->query())
        );
    }

    public function store(StoreCreacionRequest $request)
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

        $creacion = Creacion::create($data);

        return (new CreacionResource($creacion))
            ->response()->setStatusCode(201);
    }

    public function show(Creacion $creacion)
    {
        return new CreacionResource($creacion);
    }

    public function update(UpdateCreacionRequest $request, Creacion $creacion)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $creacion->update($data);

        return new CreacionResource($creacion->refresh());
    }

    public function destroy(Creacion $creacion)
    {
        try {
            $creacion->delete();
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

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Legalizacion;
use App\Filters\V1\LegalizacionFilter;
use App\Http\Resources\V1\LegalizacionResource;
use App\Http\Resources\V1\LegalizacionCollection;
use App\Http\Requests\V1\IndexLegalizacionRequest;
use App\Http\Requests\V1\StoreLegalizacionRequest;
use App\Http\Requests\V1\UpdateLegalizacionRequest;
use Illuminate\Database\QueryException;

class LegalizacionController extends Controller
{
    public function index(IndexLegalizacionRequest $request, LegalizacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Legalizacion::query()->orderBy('id');
        $filter->apply($request, $q);

        return new LegalizacionCollection(
            $q->paginate($perPage)->appends($request->query())
        );
    }

    public function store(StoreLegalizacionRequest $request)
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

        $legalizacion = Legalizacion::create($data);

        return (new LegalizacionResource($legalizacion))
            ->response()->setStatusCode(201);
    }

    public function show(Legalizacion $legalizacion)
    {
        return new LegalizacionResource($legalizacion);
    }

    public function update(UpdateLegalizacionRequest $request, Legalizacion $legalizacion)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $legalizacion->update($data);

        return new LegalizacionResource($legalizacion->refresh());
    }

    public function destroy(Legalizacion $legalizacion)
    {
        try {
            $legalizacion->delete();
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

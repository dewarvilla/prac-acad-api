<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fecha;
use App\Filters\V1\FechaFilter;
use App\Http\Resources\V1\FechaResource;
use App\Http\Resources\V1\FechaCollection;
use App\Http\Requests\V1\IndexFechaRequest;
use App\Http\Requests\V1\StoreFechaRequest;
use App\Http\Requests\V1\UpdateFechaRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class FechaController extends Controller
{
    public function index(IndexFechaRequest $request, FechaFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);

        $q = Fecha::query();
        $filter->apply($request, $q);

        if ($perPage > 0) {
            return new FechaCollection(
                $q->paginate($perPage)->appends($request->query())
            );
        }
        
        return FechaResource::collection($q->get());
    }

    public function store(StoreFechaRequest $request)
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

        $fecha = Fecha::create($data);

        return (new FechaResource($fecha))
            ->response()->setStatusCode(201);
    }

    public function show(Fecha $fecha)
    {
        return new FechaResource($fecha);
    }

    public function update(UpdateFechaRequest $request, Fecha $fecha)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $fecha->update($data);

        return new FechaResource($fecha->refresh());
    }

    public function destroy(Fecha $fecha)
    {
        try {
            $fecha->delete();
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

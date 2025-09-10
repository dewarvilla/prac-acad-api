<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Filters\V1\RutaFilter;
use App\Http\Resources\V1\RutaResource;
use App\Http\Resources\V1\RutaCollection;
use App\Http\Requests\V1\IndexRutaRequest;
use App\Http\Requests\V1\StoreRutaRequest;
use App\Http\Requests\V1\UpdateRutaRequest;
use Illuminate\Database\QueryException;

class RutaController extends Controller
{
    public function index(IndexRutaRequest $request, RutaFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);

        $q = Ruta::query()->orderBy('id');
        $filter->apply($request, $q);

        if ($perPage > 0) {
            return new RutaCollection(
                $q->paginate($perPage)->appends($request->query())
            );
        }
        
        return RutaResource::collection($q->get());
    }

    public function store(StoreRutaRequest $request)
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

        $ruta = Ruta::create($data);

        return (new RutaResource($ruta))
            ->response()->setStatusCode(201);
    }

    public function show(Ruta $ruta)
    {
        return new RutaResource($ruta);
    }

    public function update(UpdateRutaRequest $request, Ruta $ruta)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $ruta->update($data);

        return new RutaResource($ruta->refresh());
    }

    public function destroy(Ruta $ruta)
    {
        try {
            $ruta->delete();
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

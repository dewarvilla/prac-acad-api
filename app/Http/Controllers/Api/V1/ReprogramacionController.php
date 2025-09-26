<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Reprogramacion;
use App\Filters\V1\ReprogramacionFilter;
use App\Http\Resources\V1\ReprogramacionResource;
use App\Http\Resources\V1\ReprogramacionCollection;
use App\Http\Requests\V1\IndexReprogramacionRequest;
use App\Http\Requests\V1\StoreReprogramacionRequest;
use App\Http\Requests\V1\UpdateReprogramacionRequest;

class ReprogramacionController extends Controller
{
    public function index(IndexReprogramacionRequest $request, ReprogramacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Reprogramacion::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new ReprogramacionCollection($q->paginate($perPage)->appends($request->query()))
            : ReprogramacionResource::collection($q->get());
    }

    public function store(StoreReprogramacionRequest $request)
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

        $reprogramacion = Reprogramacion::create($data);

        return (new ReprogramacionResource($reprogramacion))
            ->response()->setStatusCode(201);
    }

    public function show(Reprogramacion $reprogramacion)
    {
        return new ReprogramacionResource($reprogramacion);
    }

    public function update(UpdateReprogramacionRequest $request, Reprogramacion $reprogramacion)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $reprogramacion->update($data);

        return new ReprogramacionResource($reprogramacion->refresh());
    }

    public function destroy(Reprogramacion $reprogramacion)
    {
        $reprogramacion->usuarioborrado = auth()->id() ?? 0;
        $reprogramacion->ipborrado = request()->ip();
        $reprogramacion->save();
        $reprogramacion->delete(); // soft delete
        return response()->noContent();
    }
}

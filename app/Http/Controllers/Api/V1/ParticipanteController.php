<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Participante;
use App\Filters\V1\ParticipanteFilter;
use App\Http\Resources\V1\ParticipanteResource;
use App\Http\Resources\V1\ParticipanteCollection;
use App\Http\Requests\V1\IndexParticipanteRequest;
use App\Http\Requests\V1\StoreParticipanteRequest;
use App\Http\Requests\V1\UpdateParticipanteRequest;
use App\Http\Requests\V1\BulkDeleteParticipanteRequest;

class ParticipanteController extends Controller
{
    public function index(IndexParticipanteRequest $request, ParticipanteFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Participante::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new ParticipanteCollection($q->paginate($perPage)->appends($request->query()))
            : ParticipanteResource::collection($q->get());
    }

    public function store(StoreParticipanteRequest $request)
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

        $participante = Participante::create($data);

        return (new ParticipanteResource($participante))
            ->response()->setStatusCode(201);
    }

    public function show(Participante $participante)
    {
        return new ParticipanteResource($participante);
    }

    public function update(UpdateParticipanteRequest $request, Participante $participante)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $participante->update($data);

        return new ParticipanteResource($participante->refresh());
    }

    public function destroy(Participante $participante)
    {
        $participante->delete(); 
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteParticipanteRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Participante::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Participantes eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

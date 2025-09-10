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
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ParticipanteController extends Controller
{
    public function index(IndexParticipanteRequest $request, ParticipanteFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);

        $q = Participante::query()->orderBy('id');
        $filter->apply($request, $q);

        if ($perPage > 0) {
            return new ParticipanteCollection(
                $q->paginate($perPage)->appends($request->query())
            );
        }
        
        return ParticipanteResource::collection($q->get());
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
        try {
            $participante->delete();
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

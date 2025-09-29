<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Auxilio;
use App\Filters\V1\AuxilioFilter;
use App\Http\Resources\V1\AuxilioResource;
use App\Http\Resources\V1\AuxilioCollection;
use App\Http\Requests\V1\IndexAuxilioRequest;
use App\Http\Requests\V1\StoreAuxilioRequest;
use App\Http\Requests\V1\UpdateAuxilioRequest;
use App\Http\Requests\V1\BulkDeleteAuxilioRequest;

class AuxilioController extends Controller
{
    public function index(IndexAuxilioRequest $request, AuxilioFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Auxilio::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new AuxilioCollection($q->paginate($perPage)->appends($request->query()))
            : AuxilioResource::collection($q->get());
    }

    public function store(StoreAuxilioRequest $request)
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

        $auxilio = Auxilio::create($data);

        return (new AuxilioResource($auxilio))
            ->response()->setStatusCode(201);
    }

    public function show(Auxilio $auxilio)
    {
        return new AuxilioResource($auxilio);
    }

    public function update(UpdateAuxilioRequest $request, Auxilio $auxilio)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $auxilio->update($data);

        return new AuxilioResource($auxilio->refresh());
    }

    public function destroy(Auxilio $auxilio)
    {
        $auxilio->usuarioborrado = auth()->id() ?? 0;
        $auxilio->ipborrado = request()->ip();
        $auxilio->save();
        $auxilio->delete(); // soft delete
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteAuxilioRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        $result = \DB::transaction(function () use ($ids, $uid, $ip) {
            // marca quién borró
            Auxilio::whereIn('id', $ids)->update([
                'usuarioborrado' => $uid,
                'ipborrado'      => $ip,
                'fechamodificacion'   => now(),
                'usuariomodificacion' => $uid,
                'ipmodificacion'      => $ip,
            ]);

            // soft delete
            $deleted = 0;
            foreach (array_chunk($ids, 500) as $slice) {
                $deleted += Auxilio::whereIn('id', $slice)->delete();
            }

            return ['requested'=>count($ids), 'deleted'=>$deleted, 'not_found'=>max(0, count($ids)-$deleted)];
        });

        return response()->json(['ok'=>true,'code'=>200,'message'=>'Borrado masivo ejecutado.','data'=>$result], 200);
    }
}

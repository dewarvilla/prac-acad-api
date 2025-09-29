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
use App\Http\Requests\V1\BulkDeleteRutaRequest;

class RutaController extends Controller
{
    public function index(IndexRutaRequest $request, RutaFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Ruta::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new RutaCollection($q->paginate($perPage)->appends($request->query()))
            : RutaResource::collection($q->get());
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
        $ruta->usuarioborrado = auth()->id() ?? 0;
        $ruta->ipborrado = request()->ip();
        $ruta->save();
        $ruta->delete(); // soft delete
        return response()->noContent();
    }
    
    public function destroyBulk(BulkDeleteRutaRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        $result = \DB::transaction(function () use ($ids, $uid, $ip) {
            // marca quién borró
            Ruta::whereIn('id', $ids)->update([
                'usuarioborrado' => $uid,
                'ipborrado'      => $ip,
                'fechamodificacion'   => now(),
                'usuariomodificacion' => $uid,
                'ipmodificacion'      => $ip,
            ]);

            // soft delete
            $deleted = 0;
            foreach (array_chunk($ids, 500) as $slice) {
                $deleted += Ruta::whereIn('id', $slice)->delete();
            }

            return ['requested'=>count($ids), 'deleted'=>$deleted, 'not_found'=>max(0, count($ids)-$deleted)];
        });

        return response()->json(['ok'=>true,'code'=>200,'message'=>'Borrado masivo ejecutado.','data'=>$result], 200);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use App\Filters\V1\AjusteFilter;
use App\Http\Resources\V1\AjusteResource;
use App\Http\Resources\V1\AjusteCollection;
use App\Http\Requests\V1\IndexAjusteRequest;
use App\Http\Requests\V1\StoreAjusteRequest;
use App\Http\Requests\V1\UpdateAjusteRequest;
use App\Http\Requests\V1\BulkDeleteAjusteRequest;

class AjusteController extends Controller
{
    public function index(IndexAjusteRequest $request, AjusteFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Ajuste::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new AjusteCollection($q->paginate($perPage)->appends($request->query()))
            : AjusteResource::collection($q->get());
    }

    public function store(StoreAjusteRequest $request)
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

        $ajuste = Ajuste::create($data);

        return (new AjusteResource($ajuste))
            ->response()->setStatusCode(201);
    }

    public function show(Ajuste $ajuste)
    {
        return new AjusteResource($ajuste);
    }

    public function update(UpdateAjusteRequest $request, Ajuste $ajuste)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $ajuste->update($data);

        return new AjusteResource($ajuste->refresh());
    }

    public function destroy(Ajuste $ajuste)
    {
        $ajuste->usuarioborrado = auth()->id() ?? 0;
        $ajuste->ipborrado = request()->ip();
        $ajuste->save();
        $ajuste->delete(); // soft delete
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteAjusteRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        $result = \DB::transaction(function () use ($ids, $uid, $ip) {
            // marca quién borró
            Ajuste::whereIn('id', $ids)->update([
                'usuarioborrado' => $uid,
                'ipborrado'      => $ip,
                'fechamodificacion'   => now(),
                'usuariomodificacion' => $uid,
                'ipmodificacion'      => $ip,
            ]);

            // soft delete
            $deleted = 0;
            foreach (array_chunk($ids, 500) as $slice) {
                $deleted += Ajuste::whereIn('id', $slice)->delete();
            }

            return ['requested'=>count($ids), 'deleted'=>$deleted, 'not_found'=>max(0, count($ids)-$deleted)];
        });

        return response()->json(['ok'=>true,'code'=>200,'message'=>'Borrado masivo ejecutado.','data'=>$result], 200);
    }
}

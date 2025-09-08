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
use Illuminate\Database\QueryException;

class AjusteController extends Controller
{
    public function index(IndexAjusteRequest $request, AjusteFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Ajuste::query()->orderBy('id');
        $filter->apply($request, $q);

        return new AjusteCollection(
            $q->paginate($perPage)->appends($request->query())
        );
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
        try {
            $ajuste->delete();
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

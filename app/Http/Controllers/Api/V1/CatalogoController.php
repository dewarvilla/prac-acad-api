<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Catalogo;
use App\Filters\V1\CatalogoFilter;
use App\Http\Resources\V1\CatalogoResource;
use App\Http\Resources\V1\CatalogoCollection;
use App\Http\Requests\V1\IndexCatalogoRequest;
use App\Http\Requests\V1\StoreCatalogoRequest;
use App\Http\Requests\V1\UpdateCatalogoRequest;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function index(IndexCatalogoRequest $request, CatalogoFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Catalogo::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('facultad', $op, $like)
                  ->orWhere('programa_academico', $op, $like)
                  ->orWhere('nivel_academico', $op, $like);

                if (ctype_digit($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        return $perPage > 0
            ? new CatalogoCollection($q->paginate($perPage)->appends($request->query()))
            : CatalogoResource::collection($q->get());
    }

    public function store(StoreCatalogoRequest $request)
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

        $catalogo = Catalogo::create($data);

        return (new CatalogoResource($catalogo))
            ->response()->setStatusCode(201);
    }

    public function show(Catalogo $catalogo)
    {
        return new CatalogoResource($catalogo);
    }

    public function update(UpdateCatalogoRequest $request, Catalogo $catalogo)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $catalogo->update($data);

        return new CatalogoResource($catalogo->refresh());
    }

    public function destroy(Catalogo $catalogo)
    {
        $catalogo->delete(); // Handler 23000 -> 409
        return response()->noContent();
    }

    // (Opcional) Bulk si lo estás usando:
    public function storeBulk(BulkCatalogoRequest $request) {

    }
}

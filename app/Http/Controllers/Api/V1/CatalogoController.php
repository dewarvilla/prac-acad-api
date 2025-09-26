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
use App\Http\Requests\V1\BulkCatalogoRequest;
use App\Http\Requests\V1\BulkDeleteCatalogoRequest;
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
        $catalogo->usuarioborrado = auth()->id() ?? 0;
        $catalogo->ipborrado = request()->ip();
        $catalogo->save();
        $catalogo->delete(); // soft delete
        return response()->noContent();
    }

    public function storeBulk(BulkCatalogoRequest $request)
    {
        $items = collect($request->validated()['items'] ?? []);
        if ($items->isEmpty()) {
            return response()->json(['ok' => false, 'code' => 422, 'message' => 'No se enviaron elementos para procesar.'], 422);
        }

        $now = now();
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        // $items[*] ya trae: nivel_academico, facultad, programa_academico
        $rows = $items->map(fn ($i) => [
            'nivel_academico'     => $i['nivel_academico'],
            'facultad'            => $i['facultad'],
            'programa_academico'  => $i['programa_academico'],
            'fechacreacion'       => $now,
            'usuariocreacion'     => $uid,
            'ipcreacion'          => $ip,
            'fechamodificacion'   => $now,
            'usuariomodificacion' => $uid,
            'ipmodificacion'      => $ip,
        ]);

        // Métricas de existencia (previas)
        $existentes = Catalogo::query()
            ->whereIn('facultad', $rows->pluck('facultad')->unique()->all())
            ->whereIn('programa_academico', $rows->pluck('programa_academico')->unique()->all())
            ->get()
            ->mapWithKeys(fn ($c) => [mb_strtolower($c->facultad).'|'.mb_strtolower($c->programa_academico) => true]);

        $marcas = $rows->map(function ($r) use ($existentes) {
            $key = mb_strtolower($r['facultad']).'|'.mb_strtolower($r['programa_academico']);
            return ['key' => $key, 'existing' => $existentes->has($key)];
        });

        DB::transaction(function () use ($rows) {
            Catalogo::upsert(
                $rows->all(),
                ['programa_academico', 'facultad'],
                ['nivel_academico', 'fechamodificacion', 'usuariomodificacion', 'ipmodificacion']
            );
        });

        $affected = Catalogo::query()
            ->where(function ($q) use ($rows) {
                foreach ($rows as $r) {
                    $q->orWhere(function ($qq) use ($r) {
                        $qq->where('facultad', $r['facultad'])
                           ->where('programa_academico', $r['programa_academico']);
                    });
                }
            })
            ->get();

        $created   = $marcas->where('existing', false)->count();
        $updated   = $marcas->where('existing', true)->count();
        $processed = $rows->count();

        return CatalogoResource::collection($affected)
            ->additional([
                'meta' => [
                    'processed' => $processed,
                    'created'   => $created,
                    'updated'   => $updated,
                    'timestamp' => $now->toIso8601String(),
                ]
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function destroyBulk(BulkDeleteCatalogoRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        $result = \DB::transaction(function () use ($ids, $uid, $ip) {
            // marca quién borró
            Catalogo::whereIn('id', $ids)->update([
                'usuarioborrado' => $uid,
                'ipborrado'      => $ip,
                'fechamodificacion'   => now(),
                'usuariomodificacion' => $uid,
                'ipmodificacion'      => $ip,
            ]);

            // soft delete
            $deleted = 0;
            foreach (array_chunk($ids, 500) as $slice) {
                $deleted += Catalogo::whereIn('id', $slice)->delete();
            }

            return ['requested'=>count($ids), 'deleted'=>$deleted, 'not_found'=>max(0, count($ids)-$deleted)];
        });

        return response()->json(['ok'=>true,'code'=>200,'message'=>'Borrado masivo ejecutado.','data'=>$result], 200);
    }

}

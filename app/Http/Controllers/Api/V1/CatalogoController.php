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
    public function __construct()
    {
        $this->middleware('permission:catalogos.view,sanctum')->only(['index','show']);
        $this->middleware('permission:catalogos.create,sanctum')->only(['store','storeBulk']);
        $this->middleware('permission:catalogos.edit,sanctum')->only(['update']);
        $this->middleware('permission:catalogos.delete,sanctum')->only(['destroy','destroyBulk']);
    }

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
        $catalogo->delete(); 
        return response()->noContent();
    }

    public function storeBulk(BulkCatalogoRequest $request)
    {
        $items = collect($request->validated()['items'] ?? []);
        if ($items->isEmpty()) {
            return response()->json([
                'ok' => false, 'code' => 422, 'message' => 'No se enviaron elementos para procesar.'
            ], 422);
        }

        $now = now();
        $uid = auth()->id() ?? 0;
        $ip  = $request->ip();

        $normalize = function (string $s): string {
            return preg_replace('/\s+/u', ' ', trim($s));
        };

        $rows = $items->map(function ($i) use ($now, $uid, $ip, $normalize) {
            $fac = $normalize($i['facultad']);
            $pro = $normalize($i['programa_academico']);
            return [
                'nivel_academico'     => $i['nivel_academico'],
                'facultad'            => $fac,
                'programa_academico'  => $pro,
                'fechacreacion'       => $now,
                'usuariocreacion'     => $uid,
                'ipcreacion'          => $ip,
                'fechamodificacion'   => $now,
                'usuariomodificacion' => $uid,
                'ipmodificacion'      => $ip,
                '__key'               => mb_strtolower($fac).'|'.mb_strtolower($pro), // auxiliar
            ];
        });

        $existentes = \App\Models\Catalogo::query()
            ->where(function ($q) use ($rows) {
                $pairs = $rows->pluck('__key')->unique()->values();
                foreach ($pairs as $key) {
                    [$facKey, $proKey] = explode('|', $key, 2);
                    $q->orWhere(function ($qq) use ($facKey, $proKey) {
                        $qq->whereRaw('LOWER(facultad) = ?', [$facKey])
                        ->whereRaw('LOWER(programa_academico) = ?', [$proKey]);
                    });
                }
            })
            ->get()
            ->mapWithKeys(function ($c) {
                return [ mb_strtolower($c->facultad).'|'.mb_strtolower($c->programa_academico) => true ];
            });

        $marcas = $rows->map(fn($r) => [
            'key' => $r['__key'], 'existing' => $existentes->has($r['__key']),
        ]);
        \DB::transaction(function () use ($rows) {
            foreach ($rows->chunk(500) as $slice) {
                \App\Models\Catalogo::upsert(
                    $slice->map(fn($r) => collect($r)->except('__key')->all())->all(),
                    ['programa_academico', 'facultad'],      // columnas clave (únicas)
                    ['nivel_academico', 'fechamodificacion','usuariomodificacion','ipmodificacion'] // columnas a actualizar
                );
            }
        });

        $affected = \App\Models\Catalogo::query()
            ->where(function ($q) use ($rows) {
                foreach ($rows as $r) {
                    $q->orWhere(function ($qq) use ($r) {
                        $qq->where('facultad', $r['facultad'])
                        ->where('programa_academico', $r['programa_academico']);
                    });
                }
            })
            ->orderBy('facultad')
            ->orderBy('programa_academico')
            ->get();

        $created   = $marcas->where('existing', false)->count();
        $updated   = $marcas->where('existing', true)->count();
        $processed = $rows->count();

        return \App\Http\Resources\V1\CatalogoResource::collection($affected)
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

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Catalogo::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Catálogos eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Fecha;
use App\Filters\V1\FechaFilter;
use App\Http\Resources\V1\FechaResource;
use App\Http\Resources\V1\FechaCollection;
use App\Http\Requests\V1\IndexFechaRequest;
use App\Http\Requests\V1\StoreFechaRequest;
use App\Http\Requests\V1\UpdateFechaRequest;
use App\Http\Requests\V1\BulkDeleteFechaRequest;
use Illuminate\Support\Facades\DB;

class FechaController extends Controller
{   
    public function __construct()
    {
        $this->middleware('permission:fechas.view,sanctum')->only(['index','show']);
        $this->middleware('permission:fechas.create,sanctum')->only(['store']);
        $this->middleware('permission:fechas.edit,sanctum')->only(['update']);
        $this->middleware('permission:fechas.delete,sanctum')->only(['destroy','destroyBulk']);
    }

    public function index(IndexFechaRequest $request, FechaFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Fecha::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('periodo', $op, $like)
                  ->orWhere('fecha_apertura_preg', $op, $like)
                  ->orWhere('fecha_cierre_docente_preg', $op, $like)
                  ->orWhere('fecha_apertura_postg', $op, $like)
                  ->orWhere('fecha_cierre_docente_postg', $op, $like);

                if (ctype_digit($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        return $perPage > 0
            ? new FechaCollection($q->paginate($perPage)->appends($request->query()))
            : FechaResource::collection($q->get());
    }

    public function store(StoreFechaRequest $request)
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

        try {
            $fecha = Fecha::create($data);
            return (new FechaResource($fecha))->response()->setStatusCode(201);
        } catch (\Illuminate\Database\QueryException $e) {
            $code = (string) $e->getCode();
            if ($code === '23000') {
                return response()->json(['message' => 'El periodo ya existe o hay una violación de integridad.'], 422);
            }
            if ($code === '3819' || $code === '23514') {
                return response()->json(['message' => 'Las fechas no cumplen las reglas del periodo.'], 422);
            }
            throw $e;
        }
    }

    public function show(Fecha $fecha)
    {
        return new FechaResource($fecha);
    }

    public function update(UpdateFechaRequest $request, Fecha $fecha)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        try {
            $fecha->update($data);
            return new FechaResource($fecha->refresh());
        } catch (\Illuminate\Database\QueryException $e) {
            $code = (string) $e->getCode();
            if ($code === '23000') {
                return response()->json(['message' => 'El periodo ya existe o hay una violación de integridad.'], 422);
            }
            if ($code === '3819' || $code === '23514') {
                return response()->json(['message' => 'Las fechas no cumplen las reglas del periodo.'], 422);
            }
            throw $e;
        }
    }

    public function destroy(Fecha $fecha)
    {
        $fecha->delete(); 
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteFechaRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Fecha::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Fechas eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

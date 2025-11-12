<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Creacion;
use App\Models\Catalogo;
use App\Filters\V1\CreacionFilter;
use App\Http\Resources\V1\CreacionResource;
use App\Http\Resources\V1\CreacionCollection;
use App\Http\Requests\V1\IndexCreacionRequest;
use App\Http\Requests\V1\StoreCreacionRequest;
use App\Http\Requests\V1\UpdateCreacionRequest;
use App\Http\Requests\V1\BulkDeleteCreacionRequest;
use App\Exceptions\ConflictException;
use Illuminate\Support\Facades\DB;

class CreacionController extends Controller
{   
    public function __construct()
    {
        $this->middleware('permission:creaciones.view,sanctum')->only(['index','show']);
        $this->middleware('permission:creaciones.create,sanctum')->only(['store']);
        $this->middleware('permission:creaciones.edit,sanctum')->only(['update']);
        $this->middleware('permission:creaciones.delete,sanctum')->only(['destroy','destroyBulk']);
    }

    public function index(IndexCreacionRequest $request, CreacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);
        $q = Creacion::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term   = (string) $request->query('q');
            $driver = DB::connection()->getDriverName();
            $op     = $driver === 'pgsql' ? 'ilike' : 'like';
            $like   = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('nombre_practica', $op, $like)
                   ->orWhere('programa_academico', $op, $like)
                   ->orWhere('estado_practica', $op, $like)
                   ->orWhere('estado_depart', $op, $like)
                   ->orWhere('estado_consejo_facultad', $op, $like)
                   ->orWhere('estado_consejo_academico', $op, $like);

                if (ctype_digit($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        $sort = $request->query('sort', '-id');
        $dir  = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $field = ltrim($sort, '-');

        $sortMap = [
            'id'                => 'id',
            'nombrePractica'    => 'nombre_practica',
            'programaAcademico' => 'programa_academico',
            'estadoPractica'    => 'estado_practica',
        ];

        if (isset($sortMap[$field])) {
            if ($field === 'programaAcademico' && DB::connection()->getDriverName() === 'mysql') {
                $q->orderByRaw("CONVERT(programa_academico USING utf8mb4) COLLATE utf8mb4_spanish2_ci {$dir}");
            } else {
                $q->orderBy($sortMap[$field], $dir);
            }
        } else {
            $q->orderBy('id', 'desc');
        }

        return $perPage > 0
            ? new CreacionCollection($q->paginate($perPage)->appends($request->query()))
            : CreacionResource::collection($q->get());
    }

    public function store(StoreCreacionRequest $request)
    {
        $now = now();
        $cat = Catalogo::findOrFail($request->input('catalogo_id'));

        $data = $request->validated() + [
            'facultad'            => $cat->facultad,
            'programa_academico'  => $cat->programa_academico,
            'nivel_academico'     => $cat->nivel_academico ?? null,
            'fechacreacion'       => $now,
            'fechamodificacion'   => $now,
            'usuariocreacion'     => auth()->id() ?? 0,
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipcreacion'          => $request->ip(),
            'ipmodificacion'      => $request->ip(),
        ];

        try {
            $creacion = Creacion::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains(strtolower($e->getMessage()), 'creaciones_catalogo_nombre_unique')) {
                throw new \App\Exceptions\ConflictException('Ya existe una práctica con ese nombre en este catálogo.');
            }
            throw $e;
        }

        return (new CreacionResource($creacion))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Creacion $creacion)
    {
        return new CreacionResource($creacion);
    }

    public function update(UpdateCreacionRequest $request, Creacion $creacion)
    {
        $this->authorize('update', $creacion);

        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        if ($request->filled('catalogo_id')) {
            $cat = Catalogo::findOrFail($request->input('catalogo_id'));
            $data['facultad']           = $cat->facultad;
            $data['programa_academico'] = $cat->programa_academico;
            $data['nivel_academico']    = $cat->nivel_academico ?? null;
        }

        if (isset($data['nombre_practica']) || isset($data['catalogo_id'])) {
            $catId = $data['catalogo_id'] ?? $creacion->catalogo_id;
            $nom   = $data['nombre_practica'] ?? $creacion->nombre_practica;

            $dup = Creacion::where('catalogo_id', $catId)
                ->where('nombre_practica', $nom)
                ->where('id', '!=', $creacion->id)
                ->exists();

            if ($dup) {
                throw new ConflictException('Otra práctica con ese nombre ya existe en este catálogo.');
            }
        }

        $creacion->update($data);

        return new CreacionResource($creacion->refresh());
    }

    public function destroy(Creacion $creacion)
    {
        $creacion->delete(); 
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteCreacionRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Creacion::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Creaciones eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}


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
use App\Exceptions\ConflictException;
use Illuminate\Support\Facades\DB;

class CreacionController extends Controller
{
    /**
     * GET /api/v1/creaciones
     */
    public function index(IndexCreacionRequest $request, CreacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);
        $q = Creacion::query();

        // Filtros (tu filtro ya mapea params del front a columnas)
        $filter->apply($request, $q);

        // Búsqueda libre (q)
        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $driver = DB::connection()->getDriverName();
            $op   = $driver === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

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

        // Ordenamiento
        $sort = $request->query('sort', '-id'); // por defecto id desc
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
                // Ajusta la collation si usas otra
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

    /**
     * POST /api/v1/creaciones
     */
    public function store(StoreCreacionRequest $request)
    {
        $now = now();
        $cat = Catalogo::findOrFail($request->input('catalogo_id'));

        // Regla de negocio: (catalogo_id, nombre_practica) o (programa_academico, nombre_practica)
        $dup = Creacion::where('catalogo_id', $request->input('catalogo_id'))
            ->where('nombre_practica', $request->input('nombre_practica'))
            ->exists();

        if ($dup) {
            // (a) Conflicto de negocio -> 409 (Handler a través de esta excepción)
            throw new ConflictException('Ya existe una práctica con ese nombre en este catálogo.');
        }

        $data = $request->validated() + [
            'facultad'            => $cat->facultad,
            'programa_academico'  => $cat->programa_academico,
            'nivel_academico'     => $cat->nivel_academico ?? null,
            // Auditoría
            'fechacreacion'       => $now,
            'fechamodificacion'   => $now,
            'usuariocreacion'     => auth()->id() ?? 0,
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipcreacion'          => $request->ip(),
            'ipmodificacion'      => $request->ip(),
        ];

        $creacion = Creacion::create($data);

        // 201 con Resource
        return (new CreacionResource($creacion))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/creaciones/{creacion}
     */
    public function show(Creacion $creacion)
    {
        // Si no existe => ModelNotFoundException -> 404 (Handler)
        return new CreacionResource($creacion);
    }

    /**
     * PUT/PATCH /api/v1/creaciones/{creacion}
     */
    public function update(UpdateCreacionRequest $request, Creacion $creacion)
    {
        $this->authorize('update', $creacion); //Policies -> 403 (Handler)

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

        // Evitar duplicado al actualizar (misma regla que en store)
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

    /**
     * DELETE /api/v1/creaciones/{creacion}
     */
    public function destroy(Creacion $creacion)
    {
        $this->authorize('delete', $creacion); //Policies -> 403 (Handler)
        $creacion->delete(); // Si hay FK => QueryException(23000) -> 409 (Handler)
        return response()->noContent(); // 204
    }
}

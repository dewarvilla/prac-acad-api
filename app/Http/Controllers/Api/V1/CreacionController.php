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
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class CreacionController extends Controller
{
    public function index(IndexCreacionRequest $request, CreacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Creacion::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                // columnas de texto a buscar
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


        if ($sort = $request->query('sort')) {
            $dir = 'asc';
            $field = $sort;
            if (str_starts_with($sort, '-')) {
                $dir = 'desc';
                $field = substr($sort, 1);
            }

            $sortMap = [
                'id'               => 'id',
                'nombrePractica'   => 'nombre_practica',
                'programaAcademico'=> 'programa_academico',
                'estadoPractica'   => 'estado_practica',
            ];

            if (isset($sortMap[$field])) {
                if ($field === 'programaAcademico') {
                    // usa la collation disponible en tu BD (ajústala si es otra)
                    $q->orderByRaw("CONVERT(programa_academico USING utf8mb4) COLLATE utf8mb4_spanish2_ci $dir");
                } else {
                    $q->orderBy($sortMap[$field], $dir);
                }
            } else {
                $q->orderBy('id', 'asc');
            }
        } else {
            $q->orderBy('id', 'asc');
        }


        return $perPage > 0
            ? new CreacionCollection($q->paginate($perPage)->appends($request->query()))
            : CreacionResource::collection($q->get());
    }

    public function store(StoreCreacionRequest $request)
    {
        $now = now();
        $cat = Catalogo::findOrFail($request->input('catalogo_id'));

        // (Opcional) Unicidad nombre_practica + programa_academico
        $dup = Creacion::where('nombre_practica', $request->input('nombre_practica'))
                    ->where('programa_academico', $cat->programa_academico)
                    ->exists();
        if ($dup) {
            return response()->json([
                'message' => 'La combinación nombre_practica y programa_academico ya existe.'
            ], 422);
        }

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

        $creacion = Creacion::create($data);
        return (new CreacionResource($creacion))->response()->setStatusCode(201);
    }


    public function show(Creacion $creacion)
    {
        return new CreacionResource($creacion);
    }

    public function update(UpdateCreacionRequest $request, Creacion $creacion)
    {
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

        $creacion->update($data);

        return new CreacionResource($creacion->refresh());
    }

    public function destroy(Creacion $creacion)
    {
        try {
            $creacion->delete();
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

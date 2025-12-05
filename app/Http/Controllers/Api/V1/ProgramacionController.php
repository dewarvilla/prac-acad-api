<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Programacion;
use App\Filters\V1\ProgramacionFilter;
use App\Http\Resources\V1\ProgramacionResource;
use App\Http\Resources\V1\ProgramacionCollection;
use App\Http\Requests\V1\IndexProgramacionRequest;
use App\Http\Requests\V1\StoreProgramacionRequest;
use App\Http\Requests\V1\UpdateProgramacionRequest;
use App\Http\Requests\V1\BulkDeleteProgramacionRequest;
use Illuminate\Support\Facades\DB;
use App\Services\ProgramacionFirstNotificationService;

class ProgramacionController extends Controller
{   
    /** @var ProgramacionFirstNotificationService */
    protected ProgramacionFirstNotificationService $firstNotifier;

    public function __construct(ProgramacionFirstNotificationService $firstNotifier)
    {
        $this->firstNotifier = $firstNotifier;
        $this->middleware('permission:programaciones.view')->only(['index','show']);
        $this->middleware('permission:programaciones.create')->only(['store']);
        $this->middleware('permission:programaciones.edit')->only(['update']);
        $this->middleware('permission:programaciones.delete')->only(['destroy','destroyBulk']);
    }

    public function index(IndexProgramacionRequest $request, ProgramacionFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $user    = $request->user();
        $q = Programacion::visibleFor($user);
        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('nombre_practica', $op, $like)
                  ->orWhere('lugar_de_realizacion', $op, $like)
                  ->orWhere('estado_practica', $op, $like)
                  ->orWhere('estado_depart', $op, $like)
                  ->orWhere('estado_postg', $op, $like)
                  ->orWhere('estado_decano', $op, $like)
                  ->orWhere('estado_jefe_postg', $op, $like)
                  ->orWhere('estado_vice', $op, $like)
                  ->orWhere('fecha_inicio', $op, $like)
                  ->orWhere('fecha_finalizacion', $op, $like)
                  ->orWhere('numero_estudiantes', $op, $like);

                $low = strtolower($term);
                if (in_array($low, ['si','sí','true','1','no','false','0'], true)) {
                    $val = in_array($low, ['si','sí','true','1'], true) ? 1 : 0;
                    $qq->orWhere('requiere_transporte', $val);
                }

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
            'estadoPractica'    => 'estado_practica',
            'fechaInicio'       => 'fecha_inicio',
            'fechaFinalizacion' => 'fecha_finalizacion',
        ];

        if (isset($sortMap[$field])) {
            $q->orderBy($sortMap[$field], $dir);
        }

        return $perPage > 0
            ? new ProgramacionCollection($q->paginate($perPage)->appends($request->query()))
            : ProgramacionResource::collection($q->get());
    }

    public function store(StoreProgramacionRequest $request)
    {
        $this->authorize('create', Programacion::class);

        $creacion = \App\Models\Creacion::findOrFail($request->input('creacion_id'));

        $now  = now();
        $data = $request->validated();

        $data['nombre_practica'] = $creacion->nombre_practica;

        $data += [
            'fechacreacion'       => $now,
            'fechamodificacion'   => $now,
            'usuariocreacion'     => auth()->id() ?? 0,
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipcreacion'          => $request->ip(),
            'ipmodificacion'      => $request->ip(),
        ];

        $programacion = Programacion::create($data);

        $this->firstNotifier->notifyFirstApprover($programacion);

        return (new ProgramacionResource($programacion->fresh()))
            ->response()->setStatusCode(201);
    }


    public function show(Programacion $programacion)
    {
        $this->authorize('view', $programacion);
        return new ProgramacionResource($programacion);
    }

    public function update(UpdateProgramacionRequest $request, Programacion $programacion)
    {
        $this->authorize('update', $programacion);

        $user = $request->user();

        $wasRejected       = $programacion->estado_practica === 'rechazada';
        $esDocenteCreador  = $user && $user->id === $programacion->usuariocreacion;
        $esAdmin           = $user && $user->hasRole('admin'); 

        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => $user?->id ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        if ($wasRejected && ($esDocenteCreador || $esAdmin)) {
            $data['estado_practica']   = 'en_aprobacion';
            $data['estado_depart']     = 'pendiente';
            $data['estado_postg']      = 'pendiente';
            $data['estado_decano']     = 'pendiente';
            $data['estado_jefe_postg'] = 'pendiente';
            $data['estado_vice']       = 'pendiente';

            $programacion->update($data);

            $this->firstNotifier->notifyFirstApprover($programacion->fresh());
        } else {
            $programacion->update($data);
        }

        return new ProgramacionResource($programacion->refresh());
    }


    public function destroy(Programacion $programacion)
    {
        $this->authorize('delete', $programacion);

        $programacion->delete(); 

        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteProgramacionRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return DB::transaction(function () use ($ids) {
            $programaciones = Programacion::whereIn('id', $ids)->get();

            foreach ($programaciones as $programacion) {
                $this->authorize('delete', $programacion);
            }

            $deleted = Programacion::whereIn('id', $programaciones->pluck('id'))->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Programaciones eliminadas correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Filters\V1\RutaFilter;
use App\Http\Resources\V1\RutaResource;
use App\Http\Resources\V1\RutaCollection;
use App\Http\Requests\V1\IndexRutaRequest;
use App\Http\Requests\V1\StoreRutaRequest;
use App\Http\Requests\V1\UpdateRutaRequest;
use App\Http\Requests\V1\BulkDeleteRutaRequest;

class RutaController extends Controller
{
    public function index(IndexRutaRequest $request, RutaFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Ruta::query();

        $filter->apply($request, $q);

        return $perPage > 0
            ? new RutaCollection($q->paginate($perPage)->appends($request->query()))
            : RutaResource::collection($q->get());
    }

    public function store(StoreRutaRequest $request)
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
        $ruta = Ruta::create($data);
        try {
            $compute = app(\App\Services\RoutesComputeService::class);
            $metrics = $compute->computeMetrics([
                'origin' => [
                    'lat' => $ruta->origen_lat,
                    'lng' => $ruta->origen_lng
                ],
                'dest' => [
                    'lat' => $ruta->destino_lat,
                    'lng' => $ruta->destino_lng
                ],
            ]);

            if ($metrics) {
                $ruta->distancia_m = $metrics['distance_m'] ?? null;
                $ruta->duracion_s  = $metrics['duration_s'] ?? null;
                $ruta->polyline    = $metrics['polyline'] ?? null;
                $ruta->save();
            }
        } catch (\Throwable $e) {
            \Log::warning("No se pudo calcular distancia o polyline para ruta {$ruta->id}: " . $e->getMessage());
        }
        try {
            if ($ruta->polyline) {
                $syncService = app(\App\Services\RutapeajesSyncService::class);
                $categoria = $data['categoria_vehiculo'] ?? 'I';
                $res = $syncService->syncFromSocrata($ruta, $categoria);
                \Log::info("Ruta {$ruta->id} — Peajes sincronizados automáticamente ({$res['insertados']} encontrados, categoría {$categoria})");
            } else {
                \Log::info("Ruta {$ruta->id} creada sin polyline, se omitió sincronización de peajes.");
            }
        } catch (\Throwable $e) {
            \Log::error("Error al sincronizar peajes de ruta {$ruta->id}: " . $e->getMessage());
        }

        return (new \App\Http\Resources\V1\RutaResource($ruta->fresh()))
            ->response()->setStatusCode(201);
    }


    public function show(Ruta $ruta)
    {
        return new RutaResource($ruta);
    }

    public function update(UpdateRutaRequest $request, Ruta $ruta)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $ruta->update($data);

        return new RutaResource($ruta->refresh());
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete(); 
        return response()->noContent();
    }

    public function destroyBulk(BulkDeleteRutaRequest $request)
    {
        $ids = array_values(array_unique(array_map('intval', $request->input('ids', []))));

        return \DB::transaction(function () use ($ids) {

            $deleted = \App\Models\Ruta::whereIn('id', $ids)->delete();

            return response()->json([
                'ok'      => true,
                'message' => 'Rutas eliminados correctamente.',
                'counts'  => [
                    'requested' => count($ids),
                    'deleted'   => (int) $deleted,
                ],
            ], 200);
        });
    }
}

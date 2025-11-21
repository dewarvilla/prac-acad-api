<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Models\Rutapeaje;
use App\Http\Resources\V1\RutapeajeResource;
use App\Http\Resources\V1\RutapeajeCollection;
use App\Http\Requests\V1\StoreRutapeajeRequest;
use App\Http\Requests\V1\UpdateRutapeajeRequest;

class RutapeajeController extends Controller
{   
    public function __construct()
    {
        $this->middleware('permission:rutas.view')->only(['indexByRuta']);
        $this->middleware('permission:rutas.create')->only(['store']);
        $this->middleware('permission:rutas.edit')->only(['update']);
        $this->middleware('permission:rutas.delete')->only(['destroy']);
    }

    public function indexByRuta(Ruta $ruta)
    {
        return new RutapeajeCollection($ruta->peajes()->orderBy('orden_km')->get());
    }

    public function store(StoreRutapeajeRequest $request)
    {
        $now = now();
        $data = $request->validated() + ['fechacreacion'=>$now,'fechamodificacion'=>$now];
        $peaje = Rutapeaje::create($data);
        $this->recalcularTotales($peaje->ruta_id);
        return (new RutapeajeResource($peaje))->response()->setStatusCode(201);
    }

    public function update(UpdateRutapeajeRequest $request, Rutapeaje $rutapeaje)
    {
        $rutapeaje->update($request->validated() + ['fechamodificacion'=>now()]);
        $this->recalcularTotales($rutapeaje->ruta_id);
        return new RutapeajeResource($rutapeaje->refresh());
    }

    public function destroy(Rutapeaje $rutapeaje)
    {
        $rutaId = $rutapeaje->ruta_id;
        $rutapeaje->delete();
        $this->recalcularTotales($rutaId);
        return response()->noContent();
    }

    private function recalcularTotales(int $rutaId): void
    {
        $ruta = Ruta::find($rutaId);
        if (!$ruta) return;
        $ruta->update([
            'numero_peajes'    => $ruta->peajes()->count(),
            'fechamodificacion'=> now(),
        ]);
    }
}
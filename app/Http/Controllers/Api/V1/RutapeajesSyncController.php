<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Services\RutapeajesSyncService;

class RutapeajesSyncController extends Controller
{   
    public function __construct(private RutapeajesSyncService $svc)
    {
        $this->middleware('permission:rutas.edit')->only(['recalcular', 'totalCategoria']);
    }

    public function recalcular(\Illuminate\Http\Request $req, Ruta $ruta)
    {
        if (!$ruta->polyline) {
            return response()->json([
                'ok' => false,
                'message' => 'La ruta no tiene polyline guardado aún.',
            ], 400);
        }

        try {
            $categoria = strtoupper(trim($req->input('categoria', $ruta->categoria_vehiculo ?? 'I')));
            $res = $this->svc->syncFromSocrata($ruta, $categoria);

            return response()->json([
                'ok'      => true,
                'message' => "Peajes recalculados correctamente (Categoría {$categoria}).",
                'data'    => [
                    'insertados'   => $res['insertados'] ?? 0,
                    'total_valor'  => $res['total_valor'] ?? 0,
                    'num_peajes'   => $ruta->peajes()->count(),
                ],
            ]);
        } catch (\Throwable $e) {
            \Log::error("Error al sincronizar peajes: " . $e->getMessage());
            return response()->json([
                'ok' => false,
                'message' => 'Error al sincronizar peajes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function totalCategoria(\Illuminate\Http\Request $req, Ruta $ruta)
    {
        $rawCat = $req->input('cat');
        $cat    = $rawCat ? strtoupper(trim((string)$rawCat)) : null;

        $map = ['I'=>'cat_i','II'=>'cat_ii','III'=>'cat_iii','IV'=>'cat_iv','V'=>'cat_v','VI'=>'cat_vi','VII'=>'cat_vii'];

        if (!$cat) {
            $totales = [];
            foreach ($map as $k=>$col) $totales[$k] = (float) $ruta->peajes()->sum($col);
            return response()->json(['ok'=>true,'cat'=>null,'totales'=>$totales]);
        }

        if (!isset($map[$cat])) {
            return response()->json(['ok'=>false,'code'=>422,'message'=>'Categoría inválida. Use: '.implode(',',array_keys($map))], 422);
        }

        $col = $map[$cat];
        $total = (float) $ruta->peajes()->sum($col);
        $ruta->update(['valor_peajes'=>$total,'fechamodificacion'=>now()]);

        return response()->json(['ok'=>true,'cat'=>$cat,'total'=>$total]);
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Salario;
use App\Filters\V1\SalarioFilter;
use App\Http\Resources\V1\SalarioResource;
use App\Http\Resources\V1\SalarioCollection;
use App\Http\Requests\V1\IndexSalarioRequest;
use App\Http\Requests\V1\StoreSalarioRequest;
use App\Http\Requests\V1\UpdateSalarioRequest;
use Illuminate\Support\Facades\DB;

class SalarioController extends Controller
{
    public function index(IndexSalarioRequest $request, SalarioFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 0);
        $q = Salario::query();

        $filter->apply($request, $q);

        if ($request->filled('q')) {
            $term = (string) $request->query('q');
            $op   = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $like = '%'.addcslashes($term, "%_\\").'%';

            $q->where(function ($qq) use ($like, $term, $op) {
                $qq->where('anio', $op, $like)
                  ->orWhere('valor', $op, $like);

                if (ctype_digit($term)) {
                    $qq->orWhere('id', (int) $term);
                }
            });
        }

        return $perPage > 0
            ? new SalarioCollection($q->paginate($perPage)->appends($request->query()))
            : SalarioResource::collection($q->get());
    }

    public function store(StoreSalarioRequest $request)
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

        $salario = Salario::create($data);

        return (new SalarioResource($salario))
            ->response()->setStatusCode(201);
    }

    public function show(Salario $salario)
    {
        return new SalarioResource($salario);
    }

    public function update(UpdateSalarioRequest $request, Salario $salario)
    {
        $data = $request->validated() + [
            'fechamodificacion'   => now(),
            'usuariomodificacion' => auth()->id() ?? 0,
            'ipmodificacion'      => $request->ip(),
        ];

        $salario->update($data);

        return new SalarioResource($salario->refresh());
    }

    public function destroy(Salario $salario)
    {
        $salario->usuarioborrado = auth()->id() ?? 0;
        $salario->ipborrado = request()->ip();
        $salario->save();
        $salario->delete(); // soft delete
        return response()->noContent();
    }
}

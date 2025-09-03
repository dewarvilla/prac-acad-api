<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auxilio;
use App\Http\Resources\V1\AuxilioResource;
use App\Http\Resources\V1\AuxilioCollection;
use App\Filters\V1\AuxilioFilter;
use App\Http\Requests\V1\StoreAuxilioRequest;
use App\Http\Requests\V1\UpdateAuxilioRequest;

class AuxilioController extends Controller
{
    public function index(Request $request, AuxilioFilter $filter)
    {
        $perPage = (int) $request->query('per_page', 15);

        // Construir query base y aplicar filtros/sort desde el ApiFilter::apply()
        $q = Auxilio::query();
        $filter->apply($request, $q);

        // Paginación + arrastrar query string (per_page, sort, filtros)
        $auxilios = $q->paginate($perPage)->appends($request->query());

        return new AuxilioCollection($auxilios);
    }

    public function store(StoreAuxilioRequest $request)
    {
        $auxilio = Auxilio::create($request->validated());

        return (new AuxilioResource($auxilio))
            ->response()
            ->setStatusCode(201); // 201 Created
    }

    public function show(Auxilio $auxilio) // ← singular para route model binding {auxilio}
    {
        return new AuxilioResource($auxilio);
    }

    public function update(UpdateAuxilioRequest $request, Auxilio $auxilio) // ← singular
    {
        $auxilio->update($request->validated());

        // refresh() por si hay casts/timestamps
        return new AuxilioResource($auxilio->refresh());
    }

    public function destroy(Auxilio $auxilio) // singular y usa la misma var
    {
        $auxilio->delete();
        return response()->noContent(); // 204
    }
}

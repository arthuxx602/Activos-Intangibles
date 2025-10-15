<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Municipio;


class UbicacionController extends Controller
{
    public function index(Request $r)
    {
        $q = Ubicacion::query()->with(['pais:id,nombre','departamento:id,nombre,pais_id','municipio:id,nombre,departamento_id']);
        if ($r->filled('pais_id')) $q->where('pais_id', $r->pais_id);
        if ($r->filled('departamento_id')) $q->where('departamento_id', $r->departamento_id);
        if ($r->filled('municipio_id')) $q->where('municipio_id', $r->municipio_id);
        return $q->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'pais_id'         => 'required|exists:paises,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id'    => 'nullable|exists:municipios,id',
            'direccion'       => 'nullable|string|max:255',
        ]);
        return response()->json(Ubicacion::create($data), 201);
    }

    public function show(Ubicacion $ubicacion)
    {
        return $ubicacion->load(['pais','departamento','municipio']);
    }

    public function update(Request $r, Ubicacion $ubicacion)
    {
        $data = $r->validate([
            'pais_id'         => 'sometimes|exists:paises,id',
            'departamento_id' => 'sometimes|nullable|exists:departamentos,id',
            'municipio_id'    => 'sometimes|nullable|exists:municipios,id',
            'direccion'       => 'sometimes|nullable|string|max:255',
        ]);
        $ubicacion->update($data);
        return $ubicacion->fresh()->load(['pais','departamento','municipio']);
    }

    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();
        return response()->noContent();
    }
}

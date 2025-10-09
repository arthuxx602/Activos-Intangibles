<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index(Request $r)
    {
        $q = Municipio::query()->with('departamento:id,nombre,pais_id');
        if ($r->filled('departamento_id')) $q->where('departamento_id', $r->departamento_id);
        return $q->orderBy('nombre')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'departamento_id' => 'required|exists:departamentos,id',
            'nombre'          => 'required|string|max:150'
        ]);
        return response()->json(Municipio::create($data), 201);
    }

    public function show(Municipio $municipio)
    {
        return $municipio->load('departamento:id,nombre,pais_id');
    }

    public function update(Request $request, Municipio $municipio)
    {
        $data = $request->validate([
            'departamento_id' => 'sometimes|exists:departamentos,id',
            'nombre'          => 'sometimes|string|max:150'
        ]);
        $municipio->update($data);
        return $municipio->fresh()->load('departamento:id,nombre,pais_id');
    }

    public function destroy(Municipio $municipio)
    {
        $municipio->delete();
        return response()->noContent();
    }
}

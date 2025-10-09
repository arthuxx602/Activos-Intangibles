<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vinculacion;
use Illuminate\Http\Request;

class VinculacionController extends Controller
{
    public function index(Request $r)
    {
        $q = Vinculacion::query()->with(['empresa:id,nombre','proyecto:id,nombre']);
        if ($r->filled('empresa_id'))  $q->where('empresa_id', $r->empresa_id);
        if ($r->filled('proyecto_id')) $q->where('proyecto_id', $r->proyecto_id);
        return $q->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'empresa_id'  => 'required|exists:empresas,id',
            'proyecto_id' => 'required|exists:proyectos,id',
            'rol'         => 'nullable|string|max:100',
        ]);
        return response()->json(Vinculacion::create($data), 201);
    }

    public function show(Vinculacion $vinculacion) { return $vinculacion->load(['empresa','proyecto']); }

    public function update(Request $r, Vinculacion $vinculacion)
    {
        $data = $r->validate([
            'empresa_id'  => 'sometimes|exists:empresas,id',
            'proyecto_id' => 'sometimes|exists:proyectos,id',
            'rol'         => 'sometimes|nullable|string|max:100',
        ]);
        $vinculacion->update($data);
        return $vinculacion->fresh()->load(['empresa','proyecto']);
    }

    public function destroy(Vinculacion $vinculacion)
    {
        $vinculacion->delete();
        return response()->noContent();
    }
}

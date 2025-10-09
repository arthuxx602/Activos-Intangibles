<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index(Request $r)
    {
        $q = Departamento::query()->with('pais:id,nombre');
        if ($r->filled('pais_id')) $q->where('pais_id', $r->pais_id);
        return $q->orderBy('nombre')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pais_id' => 'required|exists:paises,id',
            'nombre'  => 'required|string|max:150'
        ]);
        return response()->json(Departamento::create($data), 201);
    }

    public function show(Departamento $departamento)
    {
        return $departamento->load('pais:id,nombre');
    }

    public function update(Request $request, Departamento $departamento)
    {
        $data = $request->validate([
            'pais_id' => 'sometimes|exists:paises,id',
            'nombre'  => 'sometimes|string|max:150'
        ]);
        $departamento->update($data);
        return $departamento->fresh()->load('pais:id,nombre');
    }

    public function destroy(Departamento $departamento)
    {
        $departamento->delete();
        return response()->noContent();
    }
}

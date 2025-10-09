<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Simulacion;
use Illuminate\Http\Request;

class SimulacionController extends Controller
{
    public function index(Request $r)
    {
        $q = Simulacion::query()->with('proyecto:id,nombre');
        if ($r->filled('proyecto_id')) $q->where('proyecto_id', $r->proyecto_id);
        return $q->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'tasa'        => 'required|numeric|min:0',
            'periodos'    => 'required|integer|min:1',
            'crecimiento' => 'nullable|numeric',
        ]);

        // Placeholder: luego pegamos tu lógica
        $sim = Simulacion::create($data + ['resultado' => json_encode([])]);
        return response()->json($sim, 201);
    }

    public function show(Simulacion $simulacion) { return $simulacion; }

    public function update(Request $r, Simulacion $simulacion)
    {
        $data = $r->validate([
            'tasa'        => 'sometimes|numeric|min:0',
            'periodos'    => 'sometimes|integer|min:1',
            'crecimiento' => 'sometimes|nullable|numeric',
        ]);
        $simulacion->update($data);
        return $simulacion->fresh();
    }

    public function destroy(Simulacion $simulacion)
    {
        $simulacion->delete();
        return response()->noContent();
    }
}

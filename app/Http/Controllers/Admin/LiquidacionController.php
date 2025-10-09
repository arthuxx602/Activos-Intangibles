<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Liquidacion;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class LiquidacionController extends Controller
{
    public function index(Request $r)
    {
        $q = Liquidacion::query()->with('proyecto:id,nombre');
        if ($r->filled('proyecto_id')) $q->where('proyecto_id', $r->proyecto_id);
        return $q->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'metodo'      => 'required|in:price,alemana,francesa',
            'periodos'    => 'required|integer|min:1',
            'tasa'        => 'required|numeric|min:0',
        ]);

        // Aquí luego inyectamos tu lógica real
        $liq = Liquidacion::create($data + ['resultado' => json_encode([])]);

        return response()->json($liq, 201);
    }

    public function show(Liquidacion $liquidacion) { return $liquidacion; }

    public function update(Request $r, Liquidacion $liquidacion)
    {
        $data = $r->validate([
            'metodo'   => 'sometimes|in:price,alemana,francesa',
            'periodos' => 'sometimes|integer|min:1',
            'tasa'     => 'sometimes|numeric|min:0',
        ]);
        $liquidacion->update($data);
        return $liquidacion->fresh();
    }

    public function destroy(Liquidacion $liquidacion)
    {
        $liquidacion->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tasa;
use Illuminate\Http\Request;

class TasaController extends Controller
{
    /**
     * GET /api/tasas
     * Soporta ?per_page=... y ?latest=1 (atajo para la última tasa)
     */
    public function index(Request $r)
    {
        if ($r->boolean('latest')) {
            $ultima = Tasa::orderByDesc('Id')->first();
            return $ultima ? response()->json($ultima) : response()->json(null, 204);
        }

        $per = (int)($r->input('per_page', 15));
        return Tasa::orderByDesc('Id')->paginate($per);
    }

    /**
     * GET /api/tasas/ultima
     */
    public function ultima()
    {
        $ultima = Tasa::orderByDesc('Id')->first();
        return $ultima ? response()->json($ultima) : response()->json(null, 204);
    }

    /**
     * POST /api/tasas
     * Body JSON: { "Tasa": 46.68 }  // porcentaje en número (no string con %)
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'Tasa' => 'required|numeric|between:0,1000', // ajusta límites si quieres
        ]);

        $tasa = Tasa::create([
            'Tasa'  => $data['Tasa'],
            'Fecha' => now(),
        ]);

        return response()->json([
            'message' => 'Tasa guardada correctamente.',
            'data'    => $tasa,
        ], 201);
    }

    /**
     * (Opcional) DELETE /api/tasas/{id}
     */
    public function destroy($id)
    {
        $t = Tasa::findOrFail($id);
        $t->delete();
        return response()->noContent();
    }
}

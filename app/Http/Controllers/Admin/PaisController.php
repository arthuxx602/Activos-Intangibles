<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index()
    {
        return Pais::orderBy('nombre')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nombre' => 'required|string|max:150|unique:paises,nombre']);
        return response()->json(Pais::create($data), 201);
    }

    public function show(Pais $pais) { return $pais; }

    public function update(Request $request, Pais $pais)
    {
        $data = $request->validate(['nombre' => 'required|string|max:150|unique:paises,nombre,'.$pais->id]);
        $pais->update($data);
        return $pais->fresh();
    }

    public function destroy(Pais $pais)
    {
        $tieneDepartamentos = DB::table('departamento')
            ->where('FK_ID_Pais', $pais->ID_Pais)
            ->exists();

        if ($tieneDepartamentos) {
            return response()->json([
                'message' => 'No se puede eliminar el país porque tiene departamentos asociados.'
            ], 409); // Conflict
        }

        $id = $pais->ID_Pais;
        $pais->delete();

        return response()->json([
            'message' => 'País eliminado exitosamente.',
            'id'      => $id,
        ], 200);
    }

    /**
     * (Opcional) DELETE /api/paises  body: { "ids": [..] }
     * Elimina en lote los que NO tengan departamentos asociados; reporta bloqueados.
     */
    public function destroyMany(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:pais,ID_Pais',
        ]);

        $ids = $data['ids'];

        $bloqueados = DB::table('departamento')
            ->whereIn('FK_ID_Pais', $ids)
            ->pluck('FK_ID_Pais')
            ->unique()
            ->map(fn($v) => (int)$v)
            ->all();

        $eliminables = array_values(array_diff($ids, $bloqueados));

        if (!empty($eliminables)) {
            DB::table('pais')->whereIn('ID_Pais', $eliminables)->delete();
        }

        return response()->json([
            'eliminados' => $eliminables,
            'bloqueados' => $bloqueados,
            'message'    => 'Operación completada.',
        ]);
    }
}



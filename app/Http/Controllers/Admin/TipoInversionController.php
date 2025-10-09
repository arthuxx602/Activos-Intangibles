<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoInversion;
use Illuminate\Http\Request;

class TipoInversionController extends Controller
{
    // PUT /api/tipos-inversion/{tipo_inversion}
    public function update(Request $r, TipoInversion $tipo_inversion)
    {
        $data = $r->validate([
            'Nombre'      => 'required|string|max:150',
            'Descripcion' => 'required|string|max:1000',
        ]);

        $tipo_inversion->update($data);

        return response()->json([
            'message' => 'Tipo de inversión actualizado exitosamente.',
            'data'    => $tipo_inversion->fresh(),
        ], 200);
    }

    // (Opcional) Compatibilidad con tu form legacy (POST con nombres originales)
    // POST /api/tipos-inversion/update-legacy
    public function updateLegacy(Request $r)
    {
        $data = $r->validate([
            'id_tipo'          => 'required|integer|exists:tipo,ID_TIPO',
            'nombre_tipo'      => 'required|string|max:150',
            'descripcion_tipo' => 'required|string|max:1000',
        ]);

        $t = TipoInversion::findOrFail($data['id_tipo']);
        $t->Nombre      = $data['nombre_tipo'];
        $t->Descripcion = $data['descripcion_tipo'];
        $t->save();

        return response()->json([
            'message' => 'Tipo de inversión actualizado exitosamente.',
            'data'    => $t,
        ], 200);
    }
}

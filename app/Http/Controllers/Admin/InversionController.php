<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InversionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r, \App\Models\Inversion $inversion)//upddate de editar inversion 
{
    // acepta {"Nombre":"..."} en JSON o form-data
    $data = $r->validate([
        'Nombre' => 'required|string|max:150',
        // (si editaras más campos, agrégalos aquí)
    ]);

    $inversion->update($data);

    return response()->json([
        'message' => 'Registro actualizado exitosamente.',
        'data'    => $inversion->fresh(),
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
     {
        $inversion->delete();

        return response()->json([
            'message' => 'Inversión eliminada correctamente.',
            'id' => $inversion->ID_Inversion
        ], 200);
    }

    // ✅ Eliminar varias inversiones a la vez
    public function destroyMany(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:inversion2,ID_Inversion',
        ]);

        $deleted = Inversion::whereIn('ID_Inversion', $data['ids'])->delete();

        return response()->json([
            'message' => "Se eliminaron $deleted inversiones correctamente.",
            'ids_eliminados' => $data['ids']
        ]);
    }
}


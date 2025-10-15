<?php

namespace App\Http\Controllers;

use App\Models\TipoInversion;
use Illuminate\Http\Request;

class TipoInversionController extends Controller
{
    // Mostrar lista
    public function index()
    {
        $tipos = TipoInversion::all();
        return view('admin.tipos_inversiones.index', compact('tipos'));
    }

    // Guardar nuevo registro
    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo' => 'required|string|max:255',
            'descripcion_tipo' => 'nullable|string',
        ]);

        TipoInversion::create([
            'Nombre' => $request->nombre_tipo,
            'Descripcion' => $request->descripcion_tipo,
        ]);

        return redirect()->route('tipos.index')->with('success', 'Tipo de inversión creado correctamente');
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_tipo' => 'required|string|max:255',
            'descripcion_tipo' => 'nullable|string',
        ]);

        $tipo = TipoInversion::findOrFail($id);
        $tipo->update([
            'Nombre' => $request->nombre_tipo,
            'Descripcion' => $request->descripcion_tipo,
        ]);

        return redirect()->route('tipos.index')->with('success', 'Tipo de inversión actualizado correctamente');
    }

    // Eliminar
    public function destroy($id)
    {
        $tipo = TipoInversion::findOrFail($id);
        $tipo->delete();

        return redirect()->route('tipos.index')->with('success', 'Tipo de inversión eliminado correctamente');
    }
}

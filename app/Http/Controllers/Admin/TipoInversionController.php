<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\TipoInversion;
use Illuminate\Http\Request;

class TipoInversionController extends Controller
{
    public function index(Request $request)
    {
        // Si se pide JSON (API): devolvemos TODOS (para DataTables client-side)
        // con filtro opcional ?search=...
        if ($request->wantsJson()) {
            $q = TipoInversion::query();

            if ($request->filled('search')) {
                $s = $request->string('search');
                $q->where(function($qq) use ($s) {
                    $qq->where('Nombre', 'like', "%{$s}%")
                       ->orWhere('Descripcion', 'like', "%{$s}%");
                });
            }

            return $q->orderByDesc('ID_Tipo')->get();
        }

        // Si es la vista:
        return view('tipos-inversion.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Nombre' => 'required|string|max:255',
            'Descripcion' => 'nullable|string',
        ]);

        $tipo = TipoInversion::create($data);
        return response()->json(['message' => 'Tipo creado exitosamente', 'tipo' => $tipo], 201);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoInversion::findOrFail($id);

        $data = $request->validate([
            'Nombre' => 'required|string|max:255',
            'Descripcion' => 'nullable|string',
        ]);

        $tipo->update($data);
        return response()->json(['message' => 'Tipo actualizado correctamente']);
    }

    public function destroy($id)
    {
        $tipo = TipoInversion::findOrFail($id);
        $tipo->delete();

        return response()->json(['message' => 'Tipo eliminado correctamente']);
    }
}

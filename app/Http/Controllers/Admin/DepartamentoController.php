<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use App\Models\Admin\Departamento;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) {
            $q = Pais::query();
            if ($r->filled('search')) {
                $s = $r->string('search');
                $q->where('Nombre', 'like', "%{$s}%");
            }
            return $q->orderBy('Nombre')->get();
        }

        return view('ubicacion.index'); // la vista única
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'ID_Pais' => 'required|numeric|unique:pais,ID_Pais',
            'Nombre'  => 'required|string|max:200',
        ]);

        $pais = Pais::create($data);
        return response()->json($pais, 201);
    }

    public function update(Request $r, $id)
    {
        $pais = Pais::findOrFail($id);
        $data = $r->validate([
            'Nombre' => 'required|string|max:200',
        ]);
        $pais->update($data);
        return response()->json(['message' => 'País actualizado', 'pais' => $pais]);
    }

    public function destroy($id)
    {
        Pais::findOrFail($id)->delete();
        return response()->noContent();
    }
}

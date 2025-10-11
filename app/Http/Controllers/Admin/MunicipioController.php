<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Municipio;
use Illuminate\Http\Request;

class MunicipioController extends Controller
{
    public function index(Request $r)
    {
        if ($r->wantsJson()) {
            $q = Municipio::query()->with('departamento:ID_Departamento,Nombre,FK_ID_Pais');

            if ($r->filled('departamento')) $q->where('FK_ID_Departamento', $r->integer('departamento'));
            if ($r->filled('search')) {
                $s = $r->string('search');
                $q->where('Nombre', 'like', "%{$s}%");
            }

            return $q->orderBy('Nombre')->get();
        }

        return view('ubicacion.index');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'Nombre'            => 'required|string|max:200',
            'FK_ID_Departamento'=> 'required|exists:departamento,ID_Departamento',
        ]);

        $m = Municipio::create($data);
        return response()->json($m, 201);
    }

    public function update(Request $r, $id)
    {
        $m = Municipio::findOrFail($id);
        $data = $r->validate([
            'Nombre'            => 'required|string|max:200',
            'FK_ID_Departamento'=> 'required|exists:departamento,ID_Departamento',
        ]);

        $m->update($data);
        return response()->json(['message' => 'Municipio actualizado', 'municipio' => $m]);
    }

    public function destroy($id)
    {
        Municipio::findOrFail($id)->delete();
        return response()->noContent();
    }
}

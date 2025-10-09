<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoInversion;
use Illuminate\Http\Request;

class TipoInversionController extends Controller
{
    public function index() { return TipoInversion::orderBy('nombre')->get(); }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nombre'      => 'required|string|max:150|unique:tipo_inversions,nombre',
            'descripcion' => 'nullable|string'
        ]);
        return response()->json(TipoInversion::create($data), 201);
    }

    public function show(TipoInversion $tipoInversion) { return $tipoInversion; }

    public function update(Request $r, TipoInversion $tipoInversion)
    {
        $data = $r->validate([
            'nombre'      => 'sometimes|string|max:150|unique:tipo_inversions,nombre,'.$tipoInversion->id,
            'descripcion' => 'sometimes|nullable|string'
        ]);
        $tipoInversion->update($data);
        return $tipoInversion->fresh();
    }

    public function destroy(TipoInversion $tipoInversion)
    {
        $tipoInversion->delete();
        return response()->noContent();
    }
}

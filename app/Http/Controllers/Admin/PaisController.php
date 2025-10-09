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
        $pais->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tasa;
use Illuminate\Http\Request;

class TasaController extends Controller
{
    public function index() { return Tasa::orderBy('Tasa')->get(); }

    public function store(Request $r)
    {
        $data = $r->validate([
            'tasa' => 'required|string|max:100|unique:tasas,tasa',
            'valor'  => 'required|numeric|min:0'
        ]);
        return response()->json(Tasa::create($data), 201);
    }

    public function show(Tasa $tasa) { return $tasa; }

    public function update(Request $r, Tasa $tasa)
    {
        $data = $r->validate([
            'tasa' => 'sometimes|string|max:100|unique:tasas,tasa,'.$tasa->id,
            'valor'  => 'sometimes|numeric|min:0'
        ]);
        $tasa->update($data);
        return $tasa->fresh();
    }

    public function destroy(Tasa $tasa)
    {
        $tasa->delete();
        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Empresa;
//use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $q = Empresa::query();
        if ($request->filled('search')) {
            $q->where('nombre', 'like', '%'.$request->search.'%');
        }
        return $q->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit'    => 'nullable|string|max:100|unique:empresas,nit',
            'email'  => 'nullable|email|max:255',
        ]);

        $empresa = Empresa::create($data);
        return response()->json($empresa, 201);
    }

    public function show(Empresa $empresa)
    {
        return $empresa;
    }

    public function update(Request $request, Empresa $empresa)
    {
        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'nit'    => 'sometimes|nullable|string|max:100|unique:empresas,nit,'.$empresa->id,
            'email'  => 'sometimes|nullable|email|max:255',
        ]);

        $empresa->update($data);
        return $empresa->fresh();
    }

    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->noContent();
    }
}

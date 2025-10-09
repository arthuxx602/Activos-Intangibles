<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    // GET /api/departamentos
    public function index(Request $r)
    {
        $q = Departamento::query()->with('pais:id,Nombre');

        if ($r->filled('FK_ID_Pais')) {
            $q->where('FK_ID_Pais', $r->integer('FK_ID_Pais'));
        }

        if ($r->filled('search')) {
            $q->where('Nombre', 'like', '%'.$r->input('search').'%');
        }

        return $q->orderBy('Nombre')->paginate(20);
    }

    // POST /api/departamentos
    public function store(Request $r)
    {
        $data = $r->validate([
            'Nombre'     => 'required|string|max:150',
            'FK_ID_Pais' => 'required|integer|exists:pais,ID_Pais', // ajusta si tu tabla es 'paises'
        ]);

        $dep = Departamento::create($data);

        return response()->json(
            $dep->load('pais:id,Nombre'),
            201
        );
    }

    // GET /api/departamentos/{id}
    public function show($id)
    {
        $dep = Departamento::with('pais:id,Nombre')->findOrFail($id);
        return $dep;
    }

    // PUT /api/departamentos/{id}
    public function update(Request $r, $id)
    {
        $dep = Departamento::findOrFail($id);

        $data = $r->validate([
            'Nombre'     => 'required|string|max:150',
            'FK_ID_Pais' => 'required|integer|exists:pais,ID_Pais', // cambia si tu tabla es 'paises'
        ]);

        $dep->update($data);

        return response()->json([
            'message' => 'Departamento actualizado exitosamente.',
            'data'    => $dep->fresh()->load('pais:id,Nombre'),
        ], 200);
    }

    // DELETE /api/departamentos/{id}
    // Bloquea eliminación si existen municipios asociados
    public function destroy($id)
    {
        $dep = Departamento::findOrFail($id);

        $tieneMunicipios = DB::table('municipio')
            ->where('FK_ID_Departamento', $dep->ID_Departamento)
            ->exists();

        if ($tieneMunicipios) {
            return response()->json([
                'message' => 'No se puede eliminar el departamento porque tiene municipios asociados.'
            ], 409); // Conflict
        }

        $dep->delete();

        return response()->json([
            'message' => 'Departamento eliminado exitosamente.',
            'id'      => $dep->ID_Departamento,
        ], 200);
    }

    // DELETE /api/departamentos   body: { "ids": [..] }  (opcional)
    // Elimina en lote los que NO tengan municipios asociados y reporta bloqueados.
    public function destroyMany(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:departamento,ID_Departamento',
        ]);

        $ids = $data['ids'];

        $bloqueados = DB::table('municipio')
            ->whereIn('FK_ID_Departamento', $ids)
            ->pluck('FK_ID_Departamento')
            ->unique()
            ->map(fn($v) => (int)$v)
            ->all();

        $eliminables = array_values(array_diff($ids, $bloqueados));

        if (!empty($eliminables)) {
            DB::table('departamento')
                ->whereIn('ID_Departamento', $eliminables)
                ->delete();
        }

        return response()->json([
            'eliminados' => $eliminables,
            'bloqueados' => $bloqueados,
            'message'    => 'Operación completada.',
        ]);
    }
}

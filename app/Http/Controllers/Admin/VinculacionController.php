<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VinculacionController extends Controller
{
    /**
     * GET /api/vinculaciones
     * Filtros:
     *   ?proyecto=ID_Proyecto
     *   ?usuario=ID_Usuario
     */
    public function index(Request $r)
    {
        $r->validate([
            'proyecto' => 'sometimes|integer|exists:proyecto,ID_Proyecto',
            'usuario'  => 'sometimes|integer|exists:usuario2,ID_Usuario',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $q = DB::table('proyecto_usuario as pu')
            ->join('proyecto as p', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->join('usuario2 as u', 'u.ID_Usuario', '=', 'pu.FK_ID_Usuario')
            ->select([
                'pu.FK_ID_Proyecto as proyecto_id',
                'p.Nombre as proyecto_nombre',
                'pu.FK_ID_Usuario as usuario_id',
                DB::raw("CONCAT(u.Nombre, ' ', u.Apellido) as usuario_nombre"),
            ]);

        if ($r->filled('proyecto')) {
            $q->where('pu.FK_ID_Proyecto', $r->integer('proyecto'));
        }
        if ($r->filled('usuario')) {
            $q->where('pu.FK_ID_Usuario', $r->integer('usuario'));
        }

        $perPage = $r->input('per_page', 20);
        return $q->orderBy('p.Nombre')->orderBy('u.Nombre')->paginate($perPage);
    }

    /**
     * POST /api/vinculaciones
     * Body (JSON o form-data):
     * {
     *   "proyecto": 123,
     *   "usuarios": [1001, 1002, 1003]
     * }
     * Sincroniza: elimina vínculos previos y crea los nuevos.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'proyecto'   => 'required|integer|exists:proyecto,ID_Proyecto',
            'usuarios'   => 'required|array|min:1',
            'usuarios.*' => 'integer|exists:usuario2,ID_Usuario',
        ]);

        DB::transaction(function () use ($data) {
            DB::table('proyecto_usuario')
                ->where('FK_ID_Proyecto', $data['proyecto'])
                ->delete();

            $rows = array_map(fn ($uid) => [
                'FK_ID_Proyecto' => $data['proyecto'],
                'FK_ID_Usuario'  => $uid,
            ], $data['usuarios']);

            DB::table('proyecto_usuario')->insert($rows);
        });

        return response()->json([
            'message'  => 'Usuarios vinculados correctamente al proyecto.',
            'proyecto' => $data['proyecto'],
            'usuarios' => $data['usuarios'],
        ], 201);
    }

    /**
     * DELETE /api/vinculaciones
     * Body:
     * {
     *   "proyecto": 123,
     *   "usuario":  1001
     * }
     * Elimina un vínculo puntual proyecto–usuario.
     */
    public function destroy(Request $r)
    {
        $data = $r->validate([
            'proyecto' => 'required|integer|exists:proyecto,ID_Proyecto',
            'usuario'  => 'required|integer|exists:usuario2,ID_Usuario',
        ]);

        $deleted = DB::table('proyecto_usuario')
            ->where('FK_ID_Proyecto', $data['proyecto'])
            ->where('FK_ID_Usuario',  $data['usuario'])
            ->delete();

        return response()->json([
            'message'  => $deleted ? 'Vínculo eliminado.' : 'No se encontró el vínculo.',
            'proyecto' => $data['proyecto'],
            'usuario'  => $data['usuario'],
        ], $deleted ? 200 : 404);
    }
}

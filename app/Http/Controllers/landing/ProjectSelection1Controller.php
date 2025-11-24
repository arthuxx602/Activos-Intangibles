<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectSelectionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->ID_Usuario ?? $user->id;

        $projects = DB::table('proyecto as p')
            ->join('proyecto_usuario as pu', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->where('pu.FK_ID_Usuario', $userId)
            ->select('p.ID_Proyecto', 'p.Nombre')
            ->orderBy('p.Nombre')
            ->get();

        return view('landing.eleccion-proyecto', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyecto' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        $userId = $user->ID_Usuario ?? $user->id;
        $rol    = (int) ($user->FK_ID_Rol ?? 0);
        $projId = (int) $request->proyecto;

        // Validar que el proyecto realmente pertenezca al usuario (seguridad)
        $existe = DB::table('proyecto_usuario')
            ->where('FK_ID_Usuario', $userId)
            ->where('FK_ID_Proyecto', $projId)
            ->exists();

        if (!$existe) {
            // Si viene por AJAX:
            if ($request->wantsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Proyecto inválido para este usuario.'
                ], 403);
            }
            return back()->withErrors(['proyecto' => 'Proyecto inválido para este usuario.']);
        }

        $proyecto = Proyecto::select('ID_Proyecto','Nombre')->find($projId);

        // Guardar en sesión (compatibilidad con legacy)
        session([
            'proyecto_seleccionado' => $proyecto->ID_Proyecto,
            'nombre_proyecto'       => $proyecto->Nombre,
        ]);

        // Si se llama vía AJAX, responder JSON
        if ($request->wantsJson()) {
            return response()->json([
                'ok'       => true,
                'proyecto' => $proyecto->ID_Proyecto,
                'nombre'   => $proyecto->Nombre,
                'rol'      => $rol,
            ]);
        }

        // Redirección según rol (ajusta a tus rutas reales)
        switch ($rol) {
            case 2: // Moderador
                return redirect()->route('moderador.Dashboard');
            case 3: // Inversionista
                return redirect()->route('inversionista.DashBoard_I');
            default:
                return redirect()->route('landing'); // o '/'
        }
    }
}

<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Inversion;
use App\Models\Usuario;
use App\Models\Proyecto;

class InversionistaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Usamos la misma resolución de proyecto que en Usuarios */
    private function resolveProyectoIdYNombre(Request $request): array
    {
        $proyectoId = $request->session()->get('proyecto_seleccionado');
        $proyectoNombre = $request->session()->get('nombre_proyecto');
        $idUsuarioSesion = $request->session()->get('cedula');

        if (empty($proyectoId) && !empty($idUsuarioSesion)) {
            $row = DB::table('proyecto_usuario')
                ->where('FK_ID_Usuario', $idUsuarioSesion)
                ->select('FK_ID_Proyecto')
                ->first();

            if ($row) {
                $proyectoId = $row->FK_ID_Proyecto;

                $proj = Proyecto::where('ID_Proyecto', $proyectoId)->first(['ID_Proyecto','Nombre']);
                if ($proj) {
                    $proyectoNombre = $proj->Nombre;
                    $request->session()->put('nombre_proyecto', $proyectoNombre);
                    $request->session()->put('proyecto_seleccionado', $proyectoId);
                }
            }
        }

        return [$proyectoId, $proyectoNombre];
    }

    /** GET /moderador/inversiones  */
    public function index(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        $inversiones = collect();
        $usuariosProyecto = collect();

        if ($proyectoId) {
            // Inversiones del proyecto
            $inversiones = Inversion::with(['usuario'])
                ->where('FK_ID_Proyecto', $proyectoId)
                ->orderByDesc('Fecha')
                ->paginate(10);

            // Usuarios vinculados al proyecto (para el select)
            $usuariosProyecto = Usuario::query()
                ->join('proyecto_usuario as pu', 'usuario2.ID_Usuario', '=', 'pu.FK_ID_Usuario')
                ->where('pu.FK_ID_Proyecto', $proyectoId)
                ->orderBy('Nombre')
                ->select('usuario2.ID_Usuario','usuario2.Nombre','usuario2.Apellido')
                ->get();
        }

        return view('moderador.inversiones.index', [
            'proyectoNombre'  => $proyectoNombre,
            'inversiones'     => $inversiones,
            'usuariosProyecto'=> $usuariosProyecto,
        ]);
    }

    /** POST /moderador/inversiones  */
    public function store(Request $request)
    {
        [$proyectoId] = $this->resolveProyectoIdYNombre($request);
        if (!$proyectoId) {
            return back()->withErrors('No hay proyecto activo para asociar la inversión.');
        }

        $data = $request->validate([
            'monto'       => ['required','numeric','min:0'],
            'fecha'       => ['required','date'],
            'descripcion' => ['nullable','string','max:500'],
            'id_usuario'  => ['required','integer'], // inversionista
        ]);

        Inversion::create([
            'Monto'         => $data['monto'],
            'Fecha'         => $data['fecha'],
            'Descripcion'   => $data['descripcion'] ?? null,
            'FK_ID_Usuario' => $data['id_usuario'],
            'FK_ID_Proyecto'=> $proyectoId,
        ]);

        return redirect()
            ->route('moderador.inversiones.index')
            ->with('ok', 'Inversión registrada.');
    }
}

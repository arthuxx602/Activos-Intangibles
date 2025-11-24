<?php

namespace App\Http\Controllers\Proyecto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectSelectionController extends Controller
{
    public function index(Request $request)
    {
        $cedula = $request->session()->get('cedula');
        $rol    = (int) $request->session()->get('rol');

        $proyectos = DB::table('proyecto_usuario as pu')
            ->join('proyecto as p', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->where('pu.FK_ID_Usuario', $cedula)
            ->select('p.ID_Proyecto', 'p.Nombre')
            ->orderBy('p.Nombre')
            ->get();

        if ($proyectos->isEmpty()) {
            return $this->redirectPorRol($rol)->with('error', 'No tienes proyectos asignados.');
        }

        if ($proyectos->count() === 1) {
            $unico = $proyectos->first();
            $this->guardarProyectoEnSesion($request, $unico->ID_Proyecto, $unico->Nombre);

            return $this->redirectPorRol($rol);
        }

        return view('Landing.Proyectos.elegir', compact('proyectos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id' => ['required', 'integer', 'exists:proyecto,ID_Proyecto'],
        ]);

        $proyecto = DB::table('proyecto')
            ->select('ID_Proyecto', 'Nombre')
            ->where('ID_Proyecto', $data['proyecto_id'])
            ->first();

        if ($proyecto) {
            $this->guardarProyectoEnSesion($request, $proyecto->ID_Proyecto, $proyecto->Nombre);
        }

        $rol = (int) $request->session()->get('rol');

        return $this->redirectPorRol($rol)->with('status', 'Proyecto seleccionado.');
    }

    private function guardarProyectoEnSesion(Request $request, int $id, string $nombre): void
    {
        $request->session()->put('proyecto_seleccionado', $id);
        $request->session()->put('nombre_proyecto', $nombre);
    }

    private function redirectPorRol(int $rol)
    {
        switch ($rol) {
            case 1: return redirect()->route('admin.inicio');
            case 2: return redirect()->route('moderador.inicio');
            case 3: return redirect()->route('inversionista.inicio');
            default: return redirect()->route('landing');
        }
    }
}

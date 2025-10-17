<?php

namespace App\Http\Controllers\Proyecto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyecto;

class ProjectSelectionController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();

        // Relación belongsToMany en Usuario (ver modelos más abajo)
        $proyectos = $user->proyectos()->orderBy('Nombre')->get(['ID_Proyecto','Nombre']);

        if ($proyectos->isEmpty()) {
            // Si no tiene proyectos: Admin entra, otros se sacan como hacías en legacy
            if ((int)$user->FK_ID_Rol === 1) {
                return redirect()->to('/admin'); // ajusta a tu ruta real
            }
            return redirect()->route('landing')->with('error','No tiene proyectos asignados. Contacte al administrador.');
        }

        if ($proyectos->count() === 1) {
            // Si solo hay uno, setear y redirigir de una
            $unico = $proyectos->first();
            session([
                'proyecto_seleccionado' => $unico->ID_Proyecto,
                'nombre_proyecto'       => $unico->Nombre,
            ]);
            return $this->redirectPorRol((int)$user->FK_ID_Rol);
        }

        return view('landing.proyectos.elegir', compact('proyectos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => ['required','integer','exists:proyecto,ID_Proyecto'],
        ]);

        $proyecto = Proyecto::select('ID_Proyecto','Nombre')
            ->where('ID_Proyecto', $request->proyecto_id)
            ->first();

        session([
            'proyecto_seleccionado' => $proyecto->ID_Proyecto,
            'nombre_proyecto'       => $proyecto->Nombre,
        ]);

        $rol = (int)optional(Auth::user())->FK_ID_Rol;

        return $this->redirectPorRol($rol)->with('status','Proyecto seleccionado.');
    }

    private function redirectPorRol(int $rol)
    {
        switch ($rol) {
            case 1: return redirect()->to('/admin/inicio');        // ajusta si tienes route('admin.inicio')
            case 2: return redirect()->to('/moderador/inicio');    // o route('moderador.inicio')
            case 3: return redirect()->to('/inversionista/inicio');// o route('inversionista.inicio')
            default: return redirect()->route('landing');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proyecto;

class NotasController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Relación belongsToMany en Usuario: $user->proyectos()
        $proyectos = $user->proyectos()->orderBy('Nombre')->get(['ID_Proyecto','Nombre']);

        if ($proyectos->isEmpty()) {
            if ((int)$user->FK_ID_Rol === 1) {
                return redirect()->to('/admin/inicio')
                    ->with('warning','No tiene proyectos asignados. Ingresó como Admin.');
            }
            return redirect()->route('landing')
                ->with('error','No tiene proyectos asignados. Contacte al administrador.');
        }

        // Si solo tiene 1, lo seleccionamos automáticamente
        if ($proyectos->count() === 1) {
            $p = $proyectos->first();
            session([
                'proyecto_seleccionado' => $p->ID_Proyecto,
                'nombre_proyecto'       => $p->Nombre,
            ]);
            return $this->redirectPorRol((int)$user->FK_ID_Rol)
                ->with('status','Proyecto seleccionado automáticamente.');
        }

        return view('notas.elegir-proyecto', compact('proyectos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyecto_id' => ['required','integer','exists:proyecto,ID_Proyecto'],
        ]);

        $p = Proyecto::select('ID_Proyecto','Nombre')
            ->where('ID_Proyecto', $request->proyecto_id)
            ->first();

        session([
            'proyecto_seleccionado' => $p->ID_Proyecto,
            'nombre_proyecto'       => $p->Nombre,
        ]);

        $rol = (int) optional(Auth::user())->FK_ID_Rol;

        return $this->redirectPorRol($rol)->with('status','Proyecto seleccionado.');
    }

    private function redirectPorRol(int $rol)
    {
        switch ($rol) {
            case 1: return redirect()->to('/admin/inicio');         // ajusta a tus rutas reales
            case 2: return redirect()->to('/moderador/inicio');
            case 3: return redirect()->to('/inversionista/inicio');
            default: return redirect()->route('landing');
        }
    }
}

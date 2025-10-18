<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $cedula     = $request->input('cedula');
        $contrasena = $request->input('contrasena');

        // ⚠️ En tu sistema legacy la contraseña NO está hasheada
        $user = DB::table('usuario2')
            ->select('ID_Usuario', 'FK_ID_Rol as rol', 'Nombre', 'Apellido', 'Contraseña')
            ->where('ID_Usuario', $cedula)
            ->first();

        if (!$user || $user->Contraseña !== $contrasena) {
            return back()
                ->withErrors(['cedula' => 'Credenciales incorrectas.'])
                ->withInput()
                ->with('open_login_modal', true);
        }

        // Sesión (igual que en el PHP legacy)
        Session::put('authenticated', true);
        Session::put('cedula', (int) $user->ID_Usuario);
        Session::put('rol',    (int) $user->rol);
        Session::put('nombre', $user->Nombre);
        Session::put('apellido', $user->Apellido);

        // Proyectos del usuario
        $proyectos = DB::table('proyecto_usuario as pu')
            ->join('proyecto as p', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->where('pu.FK_ID_Usuario', $user->ID_Usuario)
            ->select('p.ID_Proyecto', 'p.Nombre')
            ->get();

        $count = $proyectos->count();

        // Sin proyectos
        if ($count === 0) {
            if ((int)$user->rol === 1) {
                return redirect()->route('admin.inicio'); // Admin sin proyectos
            }
            // Otros roles: mensaje y cerramos sesión
            Session::invalidate();
            Session::regenerateToken();

            return back()
                ->withErrors(['cedula' => 'No tienes proyectos asignados. Comunícate con el administrador.'])
                ->with('open_login_modal', true);
        }

        // Un solo proyecto -> lo fijamos en sesión y vamos directo según el rol
        if ($count === 1) {
            $p = $proyectos->first();
            Session::put('proyecto_seleccionado', $p->ID_Proyecto);
            Session::put('nombre_proyecto', $p->Nombre);

            switch ((int)$user->rol) {
                case 2: return redirect()->route('moderador.inicio');
                case 3: return redirect()->route('inversionista.inicio');
                default: return redirect()->route('admin.inicio');
            }
        }

        // Varios proyectos -> ir a la página de elección
        return redirect()->route('proyectos.seleccionar');
    }
}

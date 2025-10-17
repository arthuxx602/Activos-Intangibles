<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Proyecto;
use App\Models\ProyectoUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'cedula'     => ['required'],
            'contrasena' => ['required'],
        ], [
            'cedula.required'     => 'La cédula es obligatoria',
            'contrasena.required' => 'La contraseña es obligatoria',
        ]);

        // NOTA: la BD actual guarda la contraseña en texto plano en el campo "Contraseña".
        // Lo ideal sería migrar a hash (bcrypt) más adelante.
        $usuario = Usuario::where('ID_Usuario', $data['cedula'])
            ->where('Contraseña', $data['contrasena'])
            ->first();

        if (!$usuario) {
            return back()->withErrors(['login' => 'Credenciales incorrectas'])->withInput();
        }

        // Guardar datos básicos en sesión (modelo legacy)
        Session::put('authenticated', true);
        Session::put('cedula',   $usuario->ID_Usuario);
        Session::put('rol',      (int)$usuario->FK_ID_Rol);
        Session::put('nombre',   $usuario->Nombre);
        Session::put('apellido', $usuario->Apellido);

        // Proyectos vinculados
        $proyectosIds = ProyectoUsuario::where('FK_ID_Usuario', $usuario->ID_Usuario)
            ->pluck('FK_ID_Proyecto')
            ->toArray();

        $rol = (int)$usuario->FK_ID_Rol;

        if (count($proyectosIds) === 0) {
            if ($rol === 1) {
                // Admin sin proyectos → lo dejamos entrar igual
                return redirect()->route('admin.inicio');
            }
            // Otros roles necesitan proyecto
            Session::flash('error', 'El usuario no tiene proyectos asignados. Contacte al administrador.');
            return $this->logout($request);
        }

        if (count($proyectosIds) === 1) {
            // Autoseleccionar proyecto
            $pid = $proyectosIds[0];
            $proyecto = Proyecto::find($pid);
            if ($proyecto) {
                Session::put('proyecto_seleccionado', $pid);
                Session::put('nombre_proyecto', $proyecto->Nombre);
            }

            return $this->redirigirPorRol($rol);
        }

        // Tiene múltiples proyectos → enviar a elección
        return redirect()->route('proyecto.elegir');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('landing');
    }

    private function redirigirPorRol(int $rol)
    {
        return match ($rol) {
            1 => redirect()->route('admin.inicio'),
            2 => redirect()->route('moderador.inicio'),
            3 => redirect()->route('inversionista.inicio'),
            default => redirect()->route('landing'),
        };
    }
}

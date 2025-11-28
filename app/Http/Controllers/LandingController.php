<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Si ya está logueado, redirigir según rol
        if (session('authenticated') === true) {
            return $this->redirigirPorRol(session('rol'));
        }

        return view('landing.index');
    }

    public function team()
    {
        return view('landing.team');
    }

    /**
     * Procesar Login
     */
    public function login(Request $request)
    {
        // Validación
        $request->validate([
            'Correo' => 'required|email',
            'Contraseña' => 'required'
        ]);

        // Obtener usuario por correo
        $usuario = Usuario::where('Correo', $request->Correo)->first();

        if (!$usuario) {
            return back()->withErrors(['Correo' => 'El correo no está registrado.']);
        }

        // Validar contraseña manualmente (porque tu columna se llama Contraseña)
        if (!password_verify($request->Contraseña, $usuario->Contraseña)) {
            return back()->withErrors(['Contraseña' => 'La contraseña es incorrecta.']);
        }

        // Guardar autenticación en sesión
        session([
            'authenticated' => true,
            'usuario_id' => $usuario->ID_Usuario,
            'rol' => $usuario->FK_ID_Rol,
            'nombre' => $usuario->Nombre
        ]);

        // Redirigir por rol
        return $this->redirigirPorRol($usuario->FK_ID_Rol);
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->flush();
        return redirect()->route('landing');
    }

    /**
     * Redirección según el rol
     */
    private function redirigirPorRol($rolId)
    {
        switch ((int)$rolId) {
            case 1:
                return redirect()->route('admin.inicio');
            case 2:
                return redirect()->route('moderador.inicio');
            case 3:
                return redirect()->route('inversionista.inicio');
            default:
                return redirect()->route('landing');
        }
    }
}

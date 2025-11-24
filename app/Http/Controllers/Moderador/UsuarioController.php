<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Usuario;
use App\Models\Proyecto;
use App\Models\Municipio;
use App\Models\Rol;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // si usas auth
    }

    /**
     * Resuelve el proyecto activo:
     * - Si existe en sesión, lo usa.
     * - Si no, lo infiere por la cédula en sesión y guarda nombre en sesión.
     */
    private function resolveProyectoIdYNombre(Request $request): array
    {
        $proyectoId = $request->session()->get('proyecto_seleccionado');
        $proyectoNombre = $request->session()->get('nombre_proyecto');
        $idUsuarioSesion = $request->session()->get('cedula'); // tu sesión original

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

    /**
     * GET /moderador/usuarios
     * Lista de usuarios asociados al proyecto activo + formulario de creación.
     */
    public function index(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        // Si no hay proyecto, mostramos la vista vacía con aviso
        $usuarios = collect();
        if ($proyectoId) {
            $usuarios = Usuario::query()
                ->join('proyecto_usuario as pu', 'usuario2.ID_Usuario', '=', 'pu.FK_ID_Usuario')
                ->where('pu.FK_ID_Proyecto', $proyectoId)
                ->with(['municipio:id,Nombre', 'rol:id,Nombre'])
                ->select('usuario2.*') // evita columnas duplicadas del join
                ->orderBy('Nombre')
                ->paginate(10);
        }

        // Datos para combos
        $municipios = Municipio::orderBy('Nombre')->get(['ID_Municipio','Nombre']);
        // Rol predeterminado: 3 = Inversionista (pero igual enviamos lista por si cambia)
        $roles = Rol::orderBy('Nombre')->get(['ID_Rol','Nombre']);

        return view('moderador.usuarios.index', [
            'proyectoNombre' => $proyectoNombre,
            'usuarios'       => $usuarios,
            'municipios'     => $municipios,
            'roles'          => $roles,
        ]);
    }

    /**
     * POST /moderador/usuarios
     * Crea usuario y lo asocia al proyecto activo.
     */
    public function store(Request $request)
    {
        // rol fijo 3 por tu lógica (Inversionista)
        $rolPredeterminado = 3;

        $data = $request->validate([
            'cedula'     => ['required','integer'],
            'nombre'     => ['required','string','max:120'],
            'apellido'   => ['required','string','max:120'],
            'telefono'   => ['nullable','string','max:30'],
            'correo'     => ['nullable','email','max:180'],
            'contrasena' => ['required','string','min:4','confirmed'], // usa name="contrasena_confirmation"
            'fecha'      => ['nullable','date'],
            'ciudad'     => ['required','integer'], // FK_ID_Municipio
        ]);

        // Resuelve proyecto
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);
        if (!$proyectoId) {
            return back()->withErrors('No hay proyecto activo para asociar al usuario.');
        }

        // Crear usuario
        $usuario = Usuario::create([
            'ID_Usuario'     => $data['cedula'],
            'Nombre'         => $data['nombre'],
            'Apellido'       => $data['apellido'],
            'Telefono'       => $data['telefono'] ?? null,
            'Correo'         => $data['correo']   ?? null,
            // Guardabas plano; aquí te dejo *hasheado* por seguridad:
            'Contraseña'     => Hash::make($data['contrasena']),
            'Fecha'          => $data['fecha'] ?? null,
            'FK_ID_Municipio'=> $data['ciudad'],
            'FK_ID_Rol'      => $rolPredeterminado,
        ]);

        // Asociar en pivote proyecto_usuario
        DB::table('proyecto_usuario')->insert([
            'FK_ID_Usuario'  => $usuario->ID_Usuario,
            'FK_ID_Proyecto' => $proyectoId,
        ]);

        return redirect()
            ->route('moderador.usuarios.index')
            ->with('ok', 'Usuario creado y asociado al proyecto.');
    }
}

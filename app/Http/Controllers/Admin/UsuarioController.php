<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * GET /api/usuarios
     * Filtros: ?search=..., ?from=YYYY-MM-DD, ?to=YYYY-MM-DD, ?rol=ID, ?municipio=ID
     */
    public function index(Request $r)
    {
        $q = Usuario::query()->with(['municipio','rol']);

        if ($r->filled('search')) {
            $s = $r->string('search');
            $q->where(function($qq) use ($s) {
                $qq->where('ID_Usuario', 'like', "%{$s}%")
                   ->orWhere('Nombre', 'like', "%{$s}%")
                   ->orWhere('Apellido', 'like', "%{$s}%")
                   ->orWhere('Correo', 'like', "%{$s}%");
            });
        }
        if ($r->filled('from')) $q->whereDate('Fecha', '>=', $r->date('from'));
        if ($r->filled('to'))   $q->whereDate('Fecha', '<=', $r->date('to'));
        if ($r->filled('rol'))  $q->where('FK_ID_Rol', $r->input('rol'));
        if ($r->filled('municipio')) $q->where('FK_ID_Municipio', $r->input('municipio'));

        $per = (int) $r->input('per_page', 20);
        return $q->orderByDesc('Fecha')->paginate($per);
    }

    /** GET /api/usuarios/{usuario} */
    public function show(Usuario $usuario)
    {
        return $usuario->load(['municipio','rol']);
    }

    /** POST /api/usuarios */
    public function store(Request $r)
    {
        $data = $r->validate([
            'ID_Usuario'      => 'required|integer|unique:usuario2,ID_Usuario',
            'Nombre'          => 'required|string|max:120',
            'Apellido'        => 'required|string|max:120',
            'Telefono'        => 'required|string|max:50',
            'Correo'          => 'required|email|max:190|unique:usuario2,Correo',
            'Contraseña'      => 'required|string|max:190',   // legacy (sin hash)
            'Fecha'           => 'required|date',
            'FK_ID_Municipio' => 'required|integer|exists:municipio,ID_Municipio',
            'FK_ID_Rol'       => 'required|integer|exists:rol,ID_Rol',
        ]);

        $usuario = Usuario::create($data);

        return response()->json([
            'message' => 'Usuario creado correctamente.',
            'data'    => $usuario->fresh()->load(['municipio','rol']),
        ], 201);
    }

    /** PUT /api/usuarios/{usuario} */
    public function update(Request $r, Usuario $usuario)
    {
        $data = $r->validate([
            'Nombre'          => 'sometimes|required|string|max:120',
            'Apellido'        => 'sometimes|required|string|max:120',
            'Telefono'        => 'sometimes|required|string|max:50',
            'Correo'          => 'sometimes|required|email|max:190|unique:usuario2,Correo,'.$usuario->ID_Usuario.',ID_Usuario',
            'Contraseña'      => 'sometimes|required|string|max:190',  // legacy
            'Fecha'           => 'sometimes|required|date',
            'FK_ID_Municipio' => 'sometimes|required|integer|exists:municipio,ID_Municipio',
            'FK_ID_Rol'       => 'sometimes|required|integer|exists:rol,ID_Rol',
        ]);

        $usuario->update($data);

        return response()->json([
            'message' => 'Usuario actualizado.',
            'data'    => $usuario->fresh()->load(['municipio','rol']),
        ]);
    }

    /** DELETE /api/usuarios/{usuario} */
    public function destroy(Usuario $usuario)
    {
        $id = $usuario->ID_Usuario;
        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado.',
            'id'      => $id,
        ]);
    }

    /**
     * Catálogo de roles para selects: GET /api/catalogos/roles
     */
    public function rolesCatalogo()
    {
        return Rol::orderBy('Nombre')->get(['ID_Rol','Nombre']);
    }
}

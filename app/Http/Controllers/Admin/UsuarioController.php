<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    // PUT /api/usuarios/{usuario}
    public function update(Request $r, Usuario $usuario)
    {
        // Validación (ajusta reglas si tus columnas permiten otros tamaños)
        $data = $r->validate([
            'Nombre'           => 'required|string|max:150',
            'Apellido'         => 'required|string|max:150',
            'Telefono'         => 'required|string|max:50',
            'Correo'           => [
                'required', 'string', 'email', 'max:190',
                // evita duplicados de correo en otros usuarios
                Rule::unique('usuario2', 'Correo')->ignore($usuario->getKey(), $usuario->getKeyName()),
            ],
            // si viene contraseña, la cambiamos; si no, se mantiene
            'Contraseña'       => 'nullable|string|min:6|max:190',
            'FK_ID_Municipio'  => 'required|integer|exists:municipio,ID_Municipio',
        ]);

        // Asignación simple
        $usuario->Nombre          = $data['Nombre'];
        $usuario->Apellido        = $data['Apellido'];
        $usuario->Telefono        = $data['Telefono'];
        $usuario->Correo          = $data['Correo'];
        $usuario->FK_ID_Municipio = $data['FK_ID_Municipio'];

        // Solo si vino una nueva contraseña
        if (!empty($data['Contraseña'])) {
            // ⚠️ Recomendado: almacenar hasheada (mucho más seguro)
            $usuario->{'Contraseña'} = Hash::make($data['Contraseña']);
            // Si NECESITAS mantenerla en texto plano por compatibilidad (no recomendado):
            // $usuario->{'Contraseña'} = $data['Contraseña'];
        }

        $usuario->save();

        return response()->json([
            'message' => 'Usuario actualizado exitosamente.',
            'data'    => $usuario->fresh(),
        ], 200);
    }

    // OPCIONAL: endpoint compatible con tu form legacy (POST con nombres originales)
    // POST /api/usuarios/update-legacy
    public function updateLegacy(Request $r)
    {
        $data = $r->validate([
            'id_usuario'        => 'required|integer|exists:usuario2,ID_Usuario',
            'nombre_usuario'    => 'required|string|max:150',
            'apellido_usuario'  => 'required|string|max:150',
            'telefono_usuario'  => 'required|string|max:50',
            'correo_usuario'    => [
                'required','string','email','max:190',
                Rule::unique('usuario2', 'Correo')->ignore($r->integer('id_usuario'), 'ID_Usuario'),
            ],
            'contraseña_usuario'=> 'nullable|string|min:6|max:190',
            'municipio_usuario' => 'required|integer|exists:municipio,ID_Municipio',
        ]);

        $usuario = Usuario::findOrFail($data['id_usuario']);
        $usuario->Nombre          = $data['nombre_usuario'];
        $usuario->Apellido        = $data['apellido_usuario'];
        $usuario->Telefono        = $data['telefono_usuario'];
        $usuario->Correo          = $data['correo_usuario'];
        $usuario->FK_ID_Municipio = $data['municipio_usuario'];

        if (!empty($data['contraseña_usuario'])) {
            $usuario->{'Contraseña'} = Hash::make($data['contraseña_usuario']);
            // (o en texto plano si te ves obligado, no recomendado)
        }

        $usuario->save();

        return response()->json([
            'message' => 'Usuario actualizado exitosamente.',
            'data'    => $usuario,
        ]);
    }
     public function destroy(Usuario $usuario)
    {
        $tieneVinculos = DB::table('proyecto_usuario')
            ->where('FK_ID_Usuario', $usuario->ID_Usuario)
            ->exists();

        if ($tieneVinculos) {
            return response()->json([
                'message' => 'El usuario no puede ser eliminado porque está vinculado a un proyecto/empresa.'
            ], 409); // 409 Conflict
        }

        $usuario->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente.',
            'id' => $usuario->ID_Usuario,
        ], 200);
    }

    /**
     * (Opcional) DELETE /api/usuarios  con body: { "ids": [..] }
     * Elimina en bloque los que NO tengan vínculos y reporta los bloqueados.
     */
    public function destroyMany(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:usuario2,ID_Usuario',
        ]);

        $ids = $data['ids'];

        // Separar vinculados vs no vinculados
        $vinculados = DB::table('proyecto_usuario')
            ->whereIn('FK_ID_Usuario', $ids)
            ->pluck('FK_ID_Usuario')
            ->unique()
            ->map(fn($v) => (int)$v)
            ->all();

        $eliminables = array_values(array_diff($ids, $vinculados));

        // Eliminar los no vinculados
        if (!empty($eliminables)) {
            DB::table('usuario2')->whereIn('ID_Usuario', $eliminables)->delete();
        }

        return response()->json([
            'eliminados'  => $eliminables,
            'bloqueados'  => $vinculados,
            'message'     => 'Operación completada.',
        ]);
    }
}


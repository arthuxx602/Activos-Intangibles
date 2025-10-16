<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Inversion2;
use App\Models\Tipo;

class InversionDocumentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Resuelve proyecto activo por sesión (igual a otros módulos) */
    private function resolveProyectoIdYNombre(Request $request): array
    {
        $proyectoId = $request->session()->get('proyecto_seleccionado');
        $proyectoNombre = $request->session()->get('nombre_proyecto');
        $idUsuarioSesion = $request->session()->get('cedula');

        if (empty($proyectoId) && !empty($idUsuarioSesion)) {
            $row = DB::table('proyecto_usuario')
                ->where('FK_ID_Usuario', $idUsuarioSesion)
                ->select('FK_ID_Proyecto')->first();

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

    /** GET /moderador/inversiones/registrar (form) */
    public function create(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        // Para selects (como en tu PHP original)
        $usuariosProyecto = Usuario::query()
            ->join('proyecto_usuario as pu', 'usuario2.ID_Usuario', '=', 'pu.FK_ID_Usuario')
            ->where('pu.FK_ID_Proyecto', $proyectoId)
            ->orderBy('Nombre')
            ->select('usuario2.ID_Usuario','usuario2.Nombre','usuario2.Apellido')
            ->get();

        // Tipos (si manejas catálogo)
        $tipos = Tipo::orderBy('Nombre')->get(['ID_Tipo','Nombre']);

        // Lista de proyectos (si quieres seleccionar por nombre como en legacy)
        $proyectos = Proyecto::orderBy('Nombre')->get(['ID_Proyecto','Nombre']);

        return view('moderador.inversiones.create_doc', compact(
            'proyectoNombre','usuariosProyecto','tipos','proyectos'
        ));
    }

    /** POST /moderador/inversiones/registrar (sube PDF + inserta en inversion2) */
    public function store(Request $request)
    {
        // Validaciones equivalentes a tu registrarM.php
        $data = $request->validate([
            'usuario'               => ['required','string','max:180'],
            'monto'                 => ['required','numeric','min:0'],
            'proyecto'              => ['required','string','max:180'], // nombre del proyecto (legacy)
            'tipo'                  => ['required','string','max:180'],
            'fecha_inversion'       => ['required','date'],
            'descripcion_inversion' => ['required','string','max:1000'],
            'id_usuario'            => ['required','integer'],
            'id_tipo'               => ['required','integer'],
            'archivo'               => ['required','file','mimetypes:application/pdf','max:10240'], // 10MB
        ]);

        // Verificar que el proyecto exista por NOMBRE (igual que legacy)
        $existeProyecto = Proyecto::where('Nombre', $data['proyecto'])->exists();
        if (!$existeProyecto) {
            return back()->withErrors('El proyecto no existe. Por favor, selecciona un proyecto válido.')
                         ->withInput();
        }

        // Subir PDF a storage/app/public/certificados
        // (luego: php artisan storage:link)
        $pdf = $request->file('archivo');
        $filename = uniqid('cert_').'.'.$pdf->getClientOriginalExtension();
        $pdf->storeAs('public/certificados', $filename);

        // Insertar registro en inversion2 (mantenemos "Monto_Ajustado" como vacío, como en tu PHP)
        Inversion2::create([
            'Nombre'               => $data['usuario'],
            'Monto'                => $data['monto'],
            'Monto_Ajustado'       => '', // igual que tu $montoA en legacy
            'proyecto'             => $data['proyecto'], // guardas el nombre, no el ID, igual al legacy
            'Tipo'                 => $data['tipo'],
            'Fecha'                => $data['fecha_inversion'],
            'Descripcion'          => $data['descripcion_inversion'],
            'CertificadoInversion' => $filename,
            'FK_ID_Usuario'        => $data['id_usuario'],
            'FK_ID_Tipo'           => $data['id_tipo'],
        ]);

        return redirect()
            ->route('moderador.inversiones.index')
            ->with('ok','¡Tu registro se ha completado!');
    }
    public function index(Request $request)
{
    // Proyecto activo (misma lógica que create/store)
    [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

    // Si quieres filtrar por proyecto activo, y en tu tabla guardas el NOMBRE del proyecto:
    $query = \App\Models\Inversion2::query();
    if ($proyectoNombre) {
        $query->where('proyecto', $proyectoNombre);
    }

    $inversiones = $query->orderByDesc('Fecha')->paginate(10);

    return view('moderador.inversiones.index_docs', compact('inversiones','proyectoNombre'));
}

}

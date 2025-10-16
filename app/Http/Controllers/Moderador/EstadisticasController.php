<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proyecto;

class EstadisticasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Resuelve proyecto activo (ID y Nombre) desde sesión, igual que el resto de módulos */
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
                    $request->session()->put('proyecto_seleccionado', $proyectoId);
                    $request->session()->put('nombre_proyecto', $proyectoNombre);
                }
            }
        }

        return [$proyectoId, $proyectoNombre];
    }

    /**
     * GET /moderador/estadisticas/datos-line
     * Devuelve JSON: totales anuales por tipo (2018..2030), en millones.
     * Estructura:
     * {
     *   dinero:   [13],
     *   especie:  [13],
     *   industria:[13]
     * }
     */
    public function datosLine(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        if (!$proyectoNombre) {
            return response()->json(['error' => 'Proyecto no definido en sesión'], 400);
        }

        // Query equivalente a tu SQL (agrupa por año y tipo)
        $rows = DB::table('inversion2')
            ->selectRaw('YEAR(Fecha) as Anio, FK_ID_Tipo, SUM(Monto) as TotalMonto')
            ->where('Proyecto', $proyectoNombre) // en inversion2 guardas NOMBRE del proyecto
            ->groupBy('Anio', 'FK_ID_Tipo')
            ->orderBy('Anio')
            ->get();

        // Arrays 2018..2030 (13 posiciones)
        $startYear = 2018;
        $endYear   = 2030;
        $len       = $endYear - $startYear + 1;

        $datos = [
            'dinero'    => array_fill(0, $len, 0),
            'especie'   => array_fill(0, $len, 0),
            'industria' => array_fill(0, $len, 0),
        ];

        foreach ($rows as $r) {
            $anio = (int) $r->Anio;
            $idx  = $anio - $startYear;
            if ($idx < 0 || $idx >= $len) continue;

            $millones = ((float)$r->TotalMonto) / 1_000_000;

            switch ((int)$r->FK_ID_Tipo) {
                case 1: $datos['dinero'][$idx]    = $millones; break;
                case 2: $datos['especie'][$idx]   = $millones; break;
                case 3: $datos['industria'][$idx] = $millones; break;
            }
        }

        return response()->json($datos);
    }

    /** Vista demo que consume el endpoint y dibuja la línea (opcional) */
    public function lineView(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);
        return view('moderador.estadisticas.line', compact('proyectoNombre'));
    }
}

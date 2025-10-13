<?php

namespace App\Http\Controllers\Inversionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteInversionesController extends Controller
{
    /**
     * Retorna inversiones anuales (2018–2030) por tipo en millones:
     * dinero, especie, industria.
     */
    public function inversionesAnuales(Request $request)
    {
        // Verificar autenticación (equivalente a tu session_start + check)
        if (!Auth::check() && !$request->session()->get('authenticated', false)) {
            // Si necesitas redirigir (en web.php), redirige. En API, devuelve 401.
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // Recuperar sesión
        $proyectoID     = $request->session()->get('proyecto_seleccionado');
        $proyectoNombre = $request->session()->get('nombre_proyecto');
        $idUsuario      = $request->session()->get('cedula');

        // Si no hay proyecto en sesión, buscar por la tabla pivot proyecto_usuario
        if (empty($proyectoID) && !empty($idUsuario)) {
            $fila = DB::table('proyecto_usuario')
                ->select('FK_ID_Proyecto')
                ->where('FK_ID_Usuario', $idUsuario)
                ->first();

            if ($fila) {
                $proyectoID = $fila->FK_ID_Proyecto;

                // Buscar nombre del proyecto
                $filaProyecto = DB::table('proyecto')
                    ->select('Nombre')
                    ->where('ID_Proyecto', $proyectoID)
                    ->first();

                if ($filaProyecto) {
                    $proyectoNombre = $filaProyecto->Nombre;
                    // Guardar en sesión
                    $request->session()->put('nombre_proyecto', $proyectoNombre);
                }
            }
        }

        if (empty($proyectoNombre)) {
            return response()->json(['error' => 'No se pudo determinar el proyecto.'], 422);
        }

        // Consulta: sumar por AÑO y TIPO (no por mes), filtrado por nombre del proyecto
        $rows = DB::table('inversion2')
            ->selectRaw('YEAR(Fecha) as anio, FK_ID_Tipo as tipo, SUM(Monto) as total')
            ->where('Proyecto', $proyectoNombre)
            ->groupBy('anio', 'tipo')
            ->orderBy('anio')
            ->get();

        // Inicializar arreglo 2018..2030 (13 valores)
        $fromYear = 2018;
        $toYear   = 2030;
        $length   = $toYear - $fromYear + 1;

        $datos = [
            'dinero'    => array_fill(0, $length, 0),
            'especie'   => array_fill(0, $length, 0),
            'industria' => array_fill(0, $length, 0),
        ];

        // Map de tipo
        $mapTipo = [
            1 => 'dinero',
            2 => 'especie',
            3 => 'industria',
        ];

        // Rellenar sumas anuales (en millones)
        foreach ($rows as $r) {
            if (!isset($mapTipo[$r->tipo])) {
                continue;
            }
            $idx = $r->anio - $fromYear;
            if ($idx >= 0 && $idx < $length) {
                // Sumar por si hay múltiples filas de ese año/tipo
                $datos[$mapTipo[$r->tipo]][$idx] += ($r->total / 1_000_000);
            }
        }

        return response()->json($datos);
    }
}

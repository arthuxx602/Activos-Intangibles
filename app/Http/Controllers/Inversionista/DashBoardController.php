<?php

namespace App\Http\Controllers\Inversionista;

use App\Http\Controllers\Controller;
use App\Models\Inversionista\Inversion;
use App\Models\Inversionista\Proyecto;
use App\Models\Inversionista\ProyectoUsuario;
use App\Models\Inversionista\Tasa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Vista principal del módulo inversionista.
     * Carga lista de proyectos para el select (si quieres mostrarlo).
     */
    public function index(Request $request)
    {
        $proyectos = Proyecto::orderBy('Nombre')->get(['ID_Proyecto','Nombre']);

        // Si ya tienes algo en sesión como en el legacy:
        $proyectoIdSesion = session('proyecto_seleccionado');
        $nombreProyectoSesion = session('nombre_proyecto');
        $nombre = session('nombre', 'Usuario');
        $apellido = session('apellido', '');

        return view('inversionista.index', compact('proyectos','proyectoIdSesion','nombreProyectoSesion','nombre','apellido'));
    }

    /**
     * Devuelve el proyecto_id a usar:
     * 1) request->proyecto_id
     * 2) sesión (proyecto_seleccionado)
     * 3) por usuario (cedula) en proyecto_usuario
     */
    protected function resolverProyectoId(Request $r): ?int
    {
        $proyectoId = $r->integer('proyecto_id') ?: session('proyecto_seleccionado');

        if (!$proyectoId) {
            $usuarioId = $r->input('usuario_id') ?: session('cedula');
            if ($usuarioId) {
                $proyectoId = ProyectoUsuario::where('FK_ID_Usuario', $usuarioId)->value('FK_ID_Proyecto');
            }
        }
        return $proyectoId ?: null;
    }

    /**
     * API: /api/inversionista/resumen
     * Calcula:
     *  - tasa_ajustada (última)
     *  - aportes por usuario con valor futuro a hoy
     *  - % participación, mínima, máxima, promedio
     *  - totales capital/industria y total
     *  - #usuarios vinculados
     *  - totales por tipo (1/2/3)
     *  - datos mensuales (capital/industria) del año actual
     */
    public function resumen(Request $r)
    {
        $proyectoId = $this->resolverProyectoId($r);
        if (!$proyectoId) {
            return response()->json(['message' => 'No hay proyecto seleccionado o vinculado.'], 422);
        }

        $proyecto = Proyecto::find($proyectoId);
        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado.'], 404);
        }

        // tasa ajustada: última
        $tasa = Tasa::orderByDesc('Id')->value('Tasa');
        $tasaAjustada = (float)($tasa ?: 0);

        // inversiones del proyecto (por FK o por nombre legacy)
        $rows = Inversion::query()
            ->where(function($q) use ($proyectoId) {
                $q->where('FK_ID_Proyecto', $proyectoId)
                  ->orWhere('Proyecto', function($sub) use ($proyectoId) {
                      $sub->select('Nombre')->from('proyecto')->where('ID_Proyecto', $proyectoId)->limit(1);
                  });
            })
            ->get(['Nombre','Monto','FK_ID_Tipo','Fecha']);

        $hoy = Carbon::now();
        $porUsuario = []; // Nombre => [Capital, Industria]
        $valorCapital = 0.0;
        $valorIndustria = 0.0;

        foreach ($rows as $inv) {
            $nombre = $inv->Nombre ?: '—';
            $monto = (float)$inv->Monto;
            $fecha = $inv->Fecha ? Carbon::parse($inv->Fecha) : $hoy;

            // años exactos transcurridos
            $anios = $fecha->diffInDays($hoy) / 365.0;
            $valorFuturo = $monto * pow(1 + $tasaAjustada / 100.0, $anios);

            if (!isset($porUsuario[$nombre])) {
                $porUsuario[$nombre] = ['Nombre' => $nombre, 'Capital' => 0.0, 'Industria' => 0.0];
            }

            if (in_array((int)$inv->FK_ID_Tipo, [1,2], true)) {
                $porUsuario[$nombre]['Capital'] += $valorFuturo;
                $valorCapital += $valorFuturo;
            } elseif ((int)$inv->FK_ID_Tipo === 3) {
                $porUsuario[$nombre]['Industria'] += $valorFuturo;
                $valorIndustria += $valorFuturo;
            }
        }

        $totalAportes = $valorCapital + $valorIndustria;

        // % por usuario
        $participaciones = [];
        foreach ($porUsuario as $k => $v) {
            $totU = $v['Capital'] + $v['Industria'];
            $porc = $totalAportes > 0 ? ($totU / $totalAportes) * 100.0 : 0.0;
            $porUsuario[$k]['Porcentaje'] = $porc;
            $participaciones[] = $porc;
        }

        $min = count($participaciones) ? min($participaciones) : 0;
        $max = count($participaciones) ? max($participaciones) : 0;
        $prom = count($participaciones) ? array_sum($participaciones)/count($participaciones) : 0;

        // usuarios vinculados
        $usuariosVinculados = ProyectoUsuario::where('FK_ID_Proyecto', $proyectoId)->distinct('FK_ID_Usuario')->count('FK_ID_Usuario');

        // totales por tipo (sin valor futuro, tal como legacy)
        $totTipo = Inversion::query()
            ->selectRaw('FK_ID_Tipo as tipo, SUM(Monto) as total')
            ->where(function($q) use ($proyectoId) {
                $q->where('FK_ID_Proyecto', $proyectoId)
                  ->orWhere('Proyecto', function($sub) use ($proyectoId) {
                      $sub->select('Nombre')->from('proyecto')->where('ID_Proyecto', $proyectoId)->limit(1);
                  });
            })
            ->groupBy('FK_ID_Tipo')
            ->pluck('total', 'tipo');

        $tipo1 = (float)($totTipo[1] ?? 0);
        $tipo2 = (float)($totTipo[2] ?? 0);
        $tipo3 = (float)($totTipo[3] ?? 0);

        // series mensuales (año actual)
        $year = (int)date('Y');
        $mensuales = [
            'capital'   => array_fill(0, 12, 0.0),
            'industria' => array_fill(0, 12, 0.0),
        ];

        $rowsMensuales = Inversion::query()
            ->selectRaw('MONTH(Fecha) as mes, FK_ID_Tipo as tipo, SUM(Monto) as total')
            ->whereYear('Fecha', $year)
            ->where(function($q) use ($proyectoId) {
                $q->where('FK_ID_Proyecto', $proyectoId)
                  ->orWhere('Proyecto', function($sub) use ($proyectoId) {
                      $sub->select('Nombre')->from('proyecto')->where('ID_Proyecto', $proyectoId)->limit(1);
                  });
            })
            ->groupBy('mes','tipo')
            ->get();

        foreach ($rowsMensuales as $rm) {
            $idx = max(0, min(11, (int)$rm->mes - 1));
            $key = in_array((int)$rm->tipo, [1,2], true) ? 'capital' : ((int)$rm->tipo === 3 ? 'industria' : null);
            if ($key) $mensuales[$key][$idx] += (float)$rm->total;
        }

        // salida
        return response()->json([
            'proyecto' => [
                'id'   => (int)$proyecto->ID_Proyecto,
                'name' => $proyecto->Nombre,
            ],
            'tasa_ajustada'           => $tasaAjustada,
            'valor_aportes_capital'   => $valorCapital,
            'valor_aportes_industria' => $valorIndustria,
            'total_aportes'           => $totalAportes,
            'participacion_minima'    => $min,
            'participacion_maxima'    => $max,
            'promedio_participacion'  => $prom,
            'usuarios_vinculados'     => $usuariosVinculados,
            'totales_tipos'           => [
                'tipo1' => $tipo1,
                'tipo2' => $tipo2,
                'tipo3' => $tipo3,
            ],
            'mensuales'               => $mensuales,
            'resumen_usuarios'        => array_values($porUsuario), // tabla
        ]);
    }
}

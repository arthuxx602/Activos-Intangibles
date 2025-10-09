<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Proyecto;
use App\Models\Inversion;   // tabla: inversion2
use App\Models\Tasa;        // tabla: tasa (Id, Tasa)

class SimulacionController extends Controller
{
    /**
     * GET /api/catalogos/proyectos-no-liquidados
     * Devuelve [ {ID_Proyecto, Nombre} ... ] para poblar el select.
     */
    public function proyectosNoLiquidados()
    {
        return Proyecto::query()
            ->where('liquidado', 0)
            ->orderBy('Nombre')
            ->get(['ID_Proyecto','Nombre']);
    }

    /**
     * GET /api/simulacion/resumen?proyecto_id=...
     * Calcula todo lo que tu PHP hacía: tasas, VF por inversión,
     * sumas por tipo, % por usuario, series mensuales, etc.
     */
    public function resumen(Request $r)
    {
        $data = $r->validate([
            'proyecto_id' => 'required|integer|exists:proyecto,ID_Proyecto',
        ]);

        $proyecto = Proyecto::findOrFail($data['proyecto_id']);

        // Tasa ajustada (último registro)
        $tasa = Tasa::query()->orderByDesc('Id')->value('Tasa') ?? 0.0;
        $tasaDecimal = $tasa / 100.0;

        // OJO: tu legacy filtra inversiones por NOMBRE del proyecto
        // (inversion2.Proyecto guarda el nombre). Conservamos esa compatibilidad.
        $inversiones = Inversion::query()
            ->where('Proyecto', $proyecto->Nombre)
            ->get(['Nombre','Monto','FK_ID_Tipo','Fecha']);

        // Acumulados
        $porUsuario = [];  // 'Nombre' => ['Capital'=>x, 'Industria'=>y]
        $capital = 0.0;    // tipos 1 y 2
        $industria = 0.0;  // tipo 3

        $hoy = Carbon::today();

        foreach ($inversiones as $inv) {
            $monto = (float)$inv->Monto;
            $fecha = $inv->Fecha ? Carbon::parse($inv->Fecha) : $hoy;
            // años fraccionarios para capitalización compuesta diaria/aprox anual
            $anios = $fecha->diffInDays($hoy) / 365;

            $vf = $monto * pow(1 + $tasaDecimal, $anios);

            $nombreUsr = $inv->Nombre ?: 'Desconocido';
            if (!isset($porUsuario[$nombreUsr])) {
                $porUsuario[$nombreUsr] = ['Capital'=>0.0, 'Industria'=>0.0];
            }

            if (in_array((int)$inv->FK_ID_Tipo, [1,2], true)) {
                $porUsuario[$nombreUsr]['Capital'] += $vf;
                $capital += $vf;
            } elseif ((int)$inv->FK_ID_Tipo === 3) {
                $porUsuario[$nombreUsr]['Industria'] += $vf;
                $industria += $vf;
            }
        }

        $totalAportes = $capital + $industria;

        // Porcentajes por usuario
        $tabla = [];
        $participaciones = [];
        foreach ($porUsuario as $usr => $vals) {
            $totUsr = $vals['Capital'] + $vals['Industria'];
            $pct = $totalAportes > 0 ? ($totUsr / $totalAportes) * 100.0 : 0;
            $tabla[] = [
                'Nombre'     => $usr,
                'Capital'    => round($vals['Capital']),
                'Industria'  => round($vals['Industria']),
                'Total'      => round($totUsr),
                'Porcentaje' => round($pct, 2),
            ];
            $participaciones[] = $pct;
        }

        $minPct   = $participaciones ? min($participaciones) : 0.0;
        $maxPct   = $participaciones ? max($participaciones) : 0.0;
        $avgPct   = $participaciones ? (array_sum($participaciones) / count($participaciones)) : 0.0;

        // Usuarios vinculados al proyecto (proyecto_usuario)
        $cantidadUsuarios = DB::table('proyecto_usuario')
            ->where('FK_ID_Proyecto', $proyecto->ID_Proyecto)
            ->distinct('FK_ID_Usuario')
            ->count();

        // Sumas por tipo (monto "crudo", no VF) para tarjetas rápidas
        $sumTipo = Inversion::query()
            ->select('FK_ID_Tipo', DB::raw('COALESCE(SUM(Monto),0) as total'))
            ->where('Proyecto', $proyecto->Nombre)
            ->groupBy('FK_ID_Tipo')
            ->pluck('total','FK_ID_Tipo');

        $montoTipo1 = (float)($sumTipo[1] ?? 0);
        $montoTipo2 = (float)($sumTipo[2] ?? 0);
        $montoTipo3 = (float)($sumTipo[3] ?? 0);

        // Series mensuales (capital vs industria) - por mes del año actual
        $year = (int)($r->input('year') ?: now()->year);
        $rawMensual = Inversion::query()
            ->select(DB::raw('MONTH(Fecha) as Mes'), 'FK_ID_Tipo', DB::raw('SUM(Monto) as TotalMonto'))
            ->where('Proyecto', $proyecto->Nombre)
            ->whereYear('Fecha', $year)
            ->groupBy('Mes', 'FK_ID_Tipo')
            ->orderBy('Mes')
            ->get();

        $capitalMes   = array_fill(1, 12, 0);
        $industriaMes = array_fill(1, 12, 0);
        foreach ($rawMensual as $row) {
            $mes  = (int)$row->Mes;
            $tipo = (int)$row->FK_ID_Tipo;
            if (in_array($tipo, [1,2], true)) {
                $capitalMes[$mes] += (float)$row->TotalMonto;
            } elseif ($tipo === 3) {
                $industriaMes[$mes] += (float)$row->TotalMonto;
            }
        }
        $labelsMes = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        // Serie para barras de % por usuario (ordenada desc)
        usort($tabla, fn($a,$b) => $b['Porcentaje'] <=> $a['Porcentaje']);
        $serieUsuarios = [
            'labels' => array_column($tabla, 'Nombre'),
            'data'   => array_map(fn($row) => $row['Porcentaje'], $tabla),
        ];

        return response()->json([
            'proyecto' => [
                'id'     => $proyecto->ID_Proyecto,
                'nombre' => $proyecto->Nombre,
            ],
            'tasa_ajustada' => $tasa, // %
            'cards' => [
                'cantidad_usuarios'      => $cantidadUsuarios,
                'monto_tipo1'            => round($montoTipo1),
                'monto_tipo2'            => round($montoTipo2),
                'monto_tipo3'            => round($montoTipo3),
                'valor_aportes_capital'  => round($capital),
                'valor_aportes_industria'=> round($industria),
                'total_aportes'          => round($totalAportes),
                'participacion_minima'   => round($minPct, 2),
                'participacion_maxima'   => round($maxPct, 2),
                'promedio_participacion' => round($avgPct, 2),
            ],
            'series' => [
                'aportes_donut' => [
                    'labels' => ['Capital (tipos 1+2)','Industria (tipo 3)'],
                    'data'   => [round($capital), round($industria)],
                ],
                'usuarios_porcentaje' => $serieUsuarios,
                'mensual' => [
                    'labels'   => $labelsMes,
                    'capital'  => array_values($capitalMes),
                    'industria'=> array_values($industriaMes),
                    'year'     => $year,
                ],
            ],
            'tabla' => $tabla,
        ]);
    }
}

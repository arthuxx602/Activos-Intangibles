<?php

namespace App\Http\Controllers\Inversionista;

use App\Http\Controllers\Controller;
use App\Models\Inversionista\Inversion;
use App\Models\Inversionista\Proyecto;
use App\Models\Inversionista\ProyectoUsuario;
use App\Models\Inversionista\Tasa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LiquidacionController extends Controller
{
    public function index(Request $request)
    {
        // listado para selector (si quieres mostrarlo)
        $proyectos = Proyecto::orderBy('Nombre')->get(['ID_Proyecto','Nombre']);

        return view('inversionista.liquidacion', [
            'proyectos'          => $proyectos,
            'proyectoIdSesion'   => session('proyecto_seleccionado'),
            'nombreProyecto'     => session('nombre_proyecto'),
            'nombre'             => session('nombre', 'Usuario'),
            'apellido'           => session('apellido', ''),
        ]);
    }

    /**
     * API principal que debe replicar la lógica de liquidacionl.php
     * Devuelve JSON con todos los totales necesarios para las tarjetas y tablas.
     */
  public function resumen(Request $r)
{
    // ---- 1) Resolver proyecto (igual a tu legacy) --------------------------
    $proyectoId = $r->integer('proyecto_id') ?: session('proyecto_seleccionado');

    if (!$proyectoId) {
        $usuarioId = $r->input('usuario_id') ?: session('cedula');
        if ($usuarioId) {
            $proyectoId = \App\Models\Inversionista\ProyectoUsuario::where('FK_ID_Usuario', $usuarioId)
                ->value('FK_ID_Proyecto');
        }
    }

    if (!$proyectoId) {
        return response()->json(['message' => 'No hay proyecto seleccionado o vinculado.'], 422);
    }

    $proyecto = \App\Models\Inversionista\Proyecto::find($proyectoId);
    if (!$proyecto) {
        return response()->json(['message' => 'Proyecto no encontrado.'], 404);
    }

    // En legacy, las inversiones referencian el proyecto por NOMBRE
    $proyectoNombre = $proyecto->Nombre;

    // ---- 2) Tasa (por si la usas más adelante) -----------------------------
    $tasa = (float) (\App\Models\Inversionista\Tasa::orderByDesc('Id')->value('Tasa') ?: 0);

    // ---- 3) Traer inversiones del proyecto --------------------------------
    // Tabla legacy: inversion2
    $invQuery = \App\Models\Inversionista\Inversion::query()
        ->where('Proyecto', $proyectoNombre);

    // Series por tipo (1=dinero, 2=especie, 3=industria)
    $porTipo = (clone $invQuery)
        ->selectRaw('FK_ID_Tipo, COUNT(*) as cantidad, SUM(Monto) as total')
        ->groupBy('FK_ID_Tipo')
        ->pluck('total', 'FK_ID_Tipo')
        ->all();

    $cantPorTipo = (clone $invQuery)
        ->selectRaw('FK_ID_Tipo, COUNT(*) as cantidad')
        ->groupBy('FK_ID_Tipo')
        ->pluck('cantidad', 'FK_ID_Tipo')
        ->all();

    $sumTipo1 = (float)($porTipo[1] ?? 0); // Dinero
    $sumTipo2 = (float)($porTipo[2] ?? 0); // Especie
    $sumTipo3 = (float)($porTipo[3] ?? 0); // Industria

    $cntTipo1 = (int)($cantPorTipo[1] ?? 0);
    $cntTipo2 = (int)($cantPorTipo[2] ?? 0);
    $cntTipo3 = (int)($cantPorTipo[3] ?? 0);

    // ---- 4) Grupo por socio (Nombre) y separar capital vs industria -------
    // Capital = tipos 1 y 2 (dinero + especie), Industria = tipo 3
    $filas = (clone $invQuery)
        ->selectRaw("
            Nombre,
            SUM(CASE WHEN FK_ID_Tipo IN (1,2) THEN Monto ELSE 0 END) as capital,
            SUM(CASE WHEN FK_ID_Tipo = 3 THEN Monto ELSE 0 END)       as industria,
            COUNT(*) as cantidad
        ")
        ->groupBy('Nombre')
        ->get();

    $totalCapital   = 0.0;
    $totalIndustria = 0.0;
    $detalle        = [];
    $nombres        = [];
    $cantPorSocio   = [];
    $industriaSocio = [];

    foreach ($filas as $f) {
        $cap = (float) $f->capital;
        $ind = (float) $f->industria;
        $total = $cap + $ind;

        $detalle[] = [
            'nombre'      => $f->Nombre,
            'aporte'      => $cap,        // capital
            'industria'   => $ind,        // industria
            'a_liquidar'  => $total,      // en tu liquidación legacy no había fórmula, usamos total
            'porcentaje'  => 0,           // se llena luego
        ];

        $totalCapital   += $cap;
        $totalIndustria += $ind;

        $nombres[]        = $f->Nombre;
        $cantPorSocio[]   = (int) $f->cantidad;
        $industriaSocio[] = $ind;
    }

    $totalAportes = $totalCapital + $totalIndustria;

    // ---- 5) Porcentajes y métricas min/max/promedio -----------------------
    $participaciones = [];
    foreach ($detalle as &$row) {
        $pct = $totalAportes > 0 ? ($row['a_liquidar'] / $totalAportes) * 100 : 0;
        $row['porcentaje'] = round($pct, 4);
        $participaciones[] = $row['porcentaje'];
    }
    unset($row);

    $participacionMin = count($participaciones) ? min($participaciones) : 0;
    $participacionMax = count($participaciones) ? max($participaciones) : 0;
    $participacionAvg = count($participaciones) ? (array_sum($participaciones) / count($participaciones)) : 0;

    // ---- 6) Armar respuesta para gráficas (Apex/Highcharts) ---------------
    $charts = [
        // Valor por tipo (Dinero, Especie, Industria)
        'valorTipo' => [
            'labels' => ['Dinero', 'Especie', 'Industria'],
            'series' => [ $sumTipo1, $sumTipo2, $sumTipo3 ],
        ],
        // Cantidad por tipo
        'cantidadTipo' => [
            'labels' => ['Dinero', 'Especie', 'Industria'],
            'series' => [ $cntTipo1, $cntTipo2, $cntTipo3 ],
        ],
        // Industria por socio (para barras)
        'industriaPorSocio' => [
            'labels' => $nombres,
            'series' => $industriaSocio,
        ],
        // Cantidad de inversiones por socio
        'cantidadPorSocio' => [
            'labels' => $nombres,
            'series' => $cantPorSocio,
        ],
    ];

    // ---- 7) KPI / Resumen --------------------------------------------------
    $kpis = [
        'valor_capital'      => $totalCapital,
        'valor_industria'    => $totalIndustria,
        'total_aportes'      => $totalAportes,
        'participacion_min'  => round($participacionMin, 2),
        'participacion_max'  => round($participacionMax, 2),
        'promedio_participacion' => round($participacionAvg, 2),
    ];

    // ---- 8) Salida ---------------------------------------------------------
    return response()->json([
        'proyecto' => ['id' => $proyecto->ID_Proyecto, 'name' => $proyecto->Nombre],
        'tasa'     => $tasa,
        'kpis'     => $kpis,
        // por compatibilidad con la vista que ya hicimos:
        'resumen'  => [
            'totalAportes'      => $totalAportes,
            'totalRendimientos' => 0,              // en legacy no había cálculo de rendimientos
            'totalLiquidar'     => $totalAportes,  // liquidación = total (ajusta si luego agregas lógica)
        ],
        'detalle'  => $detalle,
        'charts'   => $charts,
    ]);
}

}

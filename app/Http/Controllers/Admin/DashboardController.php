<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index()
    {
        return view('admin.dashboard.index'); // Ajusta mayúsculas según carpeta
    } /**
     * GET /api/dashboard/summary
     * Devuelve métricas para el home: total proyectos, fechas min/max, total usuarios.
     */
    public function summary()
    {
        $totales = DB::table('proyecto')->selectRaw('COUNT(*) as total_proyectos')->first();

        $fechas  = DB::table('proyecto')
            ->selectRaw('MIN(Fecha) as fecha_mas_antigua, MAX(Fecha) as fecha_mas_nueva')
            ->first();

        $usuarios = DB::table('usuario2')->selectRaw('COUNT(*) as total_usuarios')->first();

        return response()->json([
            'total_proyectos'   => (int) ($totales->total_proyectos ?? 0),
            'fecha_mas_antigua' => $fechas->fecha_mas_antigua ?? null, // primera empresa
            'fecha_mas_nueva'   => $fechas->fecha_mas_nueva ?? null,   // última empresa
            'total_usuarios'    => (int) ($usuarios->total_usuarios ?? 0),
        ]);
    }
    // (ya tienes summary())

    /**
     * GET /api/dashboard/proyectos-por-mes?year=YYYY
     * Devuelve { year, labels[12], series[12] } con cantidad de proyectos por mes.
     */
    public function proyectosPorMes(Request $r)
    {
        $year = (int) ($r->input('year') ?: date('Y'));

        // Trae pares (mes, total) del año solicitado
        $rows = DB::table('proyecto')
            ->selectRaw('MONTH(Fecha) as mes, COUNT(*) as total')
            ->whereYear('Fecha', $year)
            ->groupBy(DB::raw('MONTH(Fecha)'))
            ->pluck('total', 'mes');  // [mes => total]

        // Prepara 12 posiciones
        $series = [];
        for ($m = 1; $m <= 12; $m++) {
            $series[] = (int) ($rows[$m] ?? 0);
        }

        // Etiquetas de meses (abrevia si prefieres)
        $labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        return response()->json([
            'year'   => $year,
            'labels' => $labels,
            'series' => $series,
        ]);
    }
   
}


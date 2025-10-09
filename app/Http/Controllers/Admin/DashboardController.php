<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
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
}

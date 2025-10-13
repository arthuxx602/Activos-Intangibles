<?php

namespace App\Http\Controllers\Inversionista;

use App\Http\Controllers\Controller;
use App\Models\Inversionista\Inversion;
use App\Models\Inversionista\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Vista del gráfico ANUAL (línea/área).
     * Carga proyectos para el select.
     */
    public function lineAnual()
    {
        $proyectos = Proyecto::orderBy('Nombre')->get(['ID_Proyecto','Nombre']);
        return view('inversionista.reportes.line-anual', compact('proyectos'));
    }

    /**
     * API: /api/inversionista/datos-line-anual
     * Params:
     *  - proyecto_id (opcional)
     *  - usuario_id  (opcional; si no llega, puede intentar resolver vía sesión si la tienes)
     *
     * Salida: labels = [2018..2030]
     *         series = Dinero/Especie/Industria (en millones)
     */
    public function datosLineAnual(Request $r)
    {
        // Rango fijo 2018..2030 (13 años)
        $start = 2018;
        $end   = 2030;
        $labels = range($start, $end);

        // Resolver proyecto
        $proyectoId = $r->input('proyecto_id');
        $usuarioId  = $r->input('usuario_id') ?? session('cedula'); // compat con tu legacy

        // Si no llega proyecto_id, intenta obtener el primero vinculado al usuario (proyecto_usuario)
        if (!$proyectoId && $usuarioId) {
            $proyectoId = DB::table('proyecto_usuario')
                ->where('FK_ID_Usuario', $usuarioId)
                ->value('FK_ID_Proyecto');
        }

        // Si aún no hay proyecto, retornamos series vacías
        if (!$proyectoId) {
            return response()->json([
                'labels' => $labels,
                'series' => [
                    ['name' => 'Dinero',    'data' => array_fill(0, count($labels), 0)],
                    ['name' => 'Especie',   'data' => array_fill(0, count($labels), 0)],
                    ['name' => 'Industria', 'data' => array_fill(0, count($labels), 0)],
                ],
            ]);
        }

        // Consulta: totales por AÑO y TIPO, filtrando por proyecto
        // Nota: tu script legacy agrupaba por mes y luego sobrescribía por año.
        // Aquí lo corregimos agrupando directamente por AÑO + TIPO.
        $rows = Inversion::query()
            ->selectRaw('YEAR(Fecha) as anio, FK_ID_Tipo as tipo, SUM(Monto) as total')
            ->whereBetween(DB::raw('YEAR(Fecha)'), [$start, $end])
            ->where(function($qq) use ($proyectoId) {
                $qq->where('FK_ID_Proyecto', $proyectoId)
                   ->orWhere('Proyecto', function($sub) use ($proyectoId) {
                       $sub->select('Nombre')->from('proyecto')->where('ID_Proyecto', $proyectoId)->limit(1);
                   });
            })
            ->groupBy('anio','tipo')
            ->orderBy('anio')
            ->get();

        // Arreglos en MILLONES (÷ 1,000,000)
        $m = count($labels);
        $series = [
            'Dinero'    => array_fill(0, $m, 0),
            'Especie'   => array_fill(0, $m, 0),
            'Industria' => array_fill(0, $m, 0),
        ];

        foreach ($rows as $r2) {
            $anio = (int) $r2->anio;
            $idx  = $anio - $start;
            if ($idx < 0 || $idx >= $m) continue;

            $valorMillones = (float)$r2->total / 1000000.0;

            switch ((int)$r2->tipo) {
                case 1: $series['Dinero'][$idx]    = $valorMillones; break;
                case 2: $series['Especie'][$idx]   = $valorMillones; break;
                case 3: $series['Industria'][$idx] = $valorMillones; break;
                default: /* ignora tipos no mapeados */ break;
            }
        }

        return response()->json([
            'labels' => array_map('strval', $labels),
            'series' => [
                ['name' => 'Dinero',    'data' => $series['Dinero']],
                ['name' => 'Especie',   'data' => $series['Especie']],
                ['name' => 'Industria', 'data' => $series['Industria']],
            ],
            'proyecto_id' => (int)$proyectoId,
        ]);
    }
}

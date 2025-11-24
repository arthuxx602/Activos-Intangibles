<?php

namespace App\Http\Controllers;

use App\Models\Inversion;
use App\Models\Proyecto;
use App\Models\Inversionista\Tasa;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InversionistaController extends Controller
{
    /** Página */
    public function index()
    {
        return view('inversionista.index');
    }

    /** Catálogos para los filtros (usuarios con inversiones y proyectos) */
    public function catalogos(Request $request)
    {
        $usuarios = Usuario::query()
            ->select('ID_Usuario','Nombre','Apellido')
            ->whereHas('inversiones')
            ->orderBy('Nombre')
            ->get();

        // todos los proyectos (o solo no liquidados si quieres)
        $proyectos = Proyecto::query()
            ->select('ID_Proyecto','Nombre','liquidado')
            ->orderBy('Nombre')
            ->get();

        return compact('usuarios','proyectos');
    }

    /**
     * Resumen + detalle por tipo para un usuario (y opcional proyecto)
     * GET /api/inversionista/resumen?usuario=CC123&proyecto=10
     */
    public function resumen(Request $request)
    {
        $request->validate([
            'usuario'  => 'required',
            'proyecto' => 'nullable'
        ]);

        $usuarioId   = $request->string('usuario');
        $proyectoId  = $request->string('proyecto');
        $tasa        = Tasa::ultimaTasa(); // % anual

        $q = Inversion::query()
            ->with(['tipo:ID_TIPO,Nombre','proyecto:ID_Proyecto,Nombre','usuario:ID_Usuario,Nombre,Apellido'])
            ->where('FK_ID_Usuario', $usuarioId);

        // Soportar legacy: si no hay FK_ID_Proyecto, usamos match por nombre si nos pasan id
        if ($proyectoId) {
            $q->where(function($qq) use ($proyectoId) {
                $qq->where('FK_ID_Proyecto', $proyectoId)
                   ->orWhereHas('proyecto', fn($p)=>$p->where('ID_Proyecto', $proyectoId))
                   ->orWhere('Proyecto', function($sub) use ($proyectoId) {
                       $nombre = Proyecto::where('ID_Proyecto',$proyectoId)->value('Nombre');
                       if ($nombre) $sub->from('inversion2')->where('Proyecto',$nombre);
                   });
            });
        }

        $items = $q->orderBy('Fecha')->get();

        // Cálculo de valor ajustado por días
        $hoy = Carbon::now()->startOfDay();
        $calc = fn($monto, $fecha) => $this->valorFuturo($monto, $fecha, $hoy, $tasa);

        $detalle = [
            'dinero'   => [],
            'especie'  => [],
            'industria'=> [],
        ];

        $totales = [
            'capital'   => 0.0,    // (dinero + especie)
            'industria' => 0.0,
            'aportes'   => 0.0,
        ];

        foreach ($items as $inv) {
            $vf = $calc($inv->Monto, $inv->Fecha);
            $dias = $inv->Fecha ? $hoy->diffInDays(Carbon::parse($inv->Fecha)) : 0;

            $row = [
                'Fecha' => optional($inv->Fecha)->format('Y-m-d'),
                'Monto' => $inv->Monto,
                'ValorAjustado' => $vf,
                'Dias' => $dias,
                'TipoId' => (int) $inv->FK_ID_Tipo,
                'Tipo' => $inv->tipo->Nombre ?? '',
                'Proyecto' => $inv->proyecto->Nombre ?? ($inv->Proyecto ?? ''),
                'ID_Inversion' => $inv->ID_Inversion,
            ];

            // 1=Dinero, 2=Especie, 3=Industria (según tu legacy)
            if (in_array($row['TipoId'], [1,2])) {
                $detalle['dinero'][]  = $row;
                $totales['capital']  += $vf;
            } elseif ($row['TipoId'] === 3) {
                $detalle['industria'][] = $row;
                $totales['industria']   += $vf;
            } else {
                // si llega algo raro, lo tratamos como capital
                $detalle['especie'][] = $row;
                $totales['capital']  += $vf;
            }
        }

        $totales['aportes'] = $totales['capital'] + $totales['industria'];

        // time-line mensual (acumulado original por mes, por tipo)
        $timeline = $this->timelinePorMes($items);

        return response()->json([
            'tasa'    => $tasa,
            'detalle' => $detalle,
            'totales' => $totales,
            'timeline'=> $timeline,
        ]);
    }

    private function valorFuturo($monto, $fecha, Carbon $hoy, float $tasa): float
    {
        if (!$fecha) return (float)$monto;
        $dias = $hoy->diffInDays(Carbon::parse($fecha));
        return (float) ($monto * pow(1 + ($tasa/100), $dias/365));
    }

    private function timelinePorMes($items)
    {
        // Devuelve: labels meses y dos series (capital vs industria) por monto original (no ajustado)
        $labels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $capital   = array_fill(0, 12, 0);
        $industria = array_fill(0, 12, 0);

        foreach ($items as $inv) {
            if (!$inv->Fecha) continue;
            $mes = ((int) Carbon::parse($inv->Fecha)->format('n')) - 1;
            if (in_array((int)$inv->FK_ID_Tipo,[1,2])) $capital[$mes]   += (float)$inv->Monto;
            if ((int)$inv->FK_ID_Tipo === 3)          $industria[$mes] += (float)$inv->Monto;
        }

        return [
            'labels' => $labels,
            'capital' => $capital,
            'industria' => $industria,
        ];
    }
}

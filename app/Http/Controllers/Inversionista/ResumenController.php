<?php

namespace App\Http\Controllers\Inversionista;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Inversionista\Inversion;        // tabla legacy: inversion2
use App\Models\Inversionista\Proyecto;         // tabla legacy: proyecto
use App\Models\Inversionista\ProyectoUsuario;  // tabla legacy: proyecto_usuario
use App\Models\Inversionista\Tasa;             // tabla legacy: tasa

class ResumenController extends Controller
{
    /**
     * Resumen del inversionista (equivalente a resumnel.php)
     *
     * Parámetros opcionales:
     * - ?proyecto_id=ID
     * Si no llega, se toma de sesión 'proyecto_seleccionado' o se resuelve por vínculo del usuario (session('cedula')).
     */
    public function index(Request $r)
    {
        // 1) Resolver proyecto:
        $proyectoId = $r->integer('proyecto_id') ?: session('proyecto_seleccionado');

        if (!$proyectoId) {
            $usuarioId = $r->input('usuario_id') ?: session('cedula');
            if ($usuarioId) {
                $proyectoId = ProyectoUsuario::where('FK_ID_Usuario', $usuarioId)->value('FK_ID_Proyecto');
            }
        }

        if (!$proyectoId) {
            // Muestra una vista simple con mensaje
            return view('inversionista.resumen', [
                'error' => 'No hay proyecto seleccionado o vinculado.',
            ]);
        }

        $proyecto = Proyecto::find($proyectoId);
        if (!$proyecto) {
            return view('inversionista.resumen', [
                'error' => 'Proyecto no encontrado.',
            ]);
        }

        // En legacy, las inversiones referencian el proyecto por NOMBRE
        $proyectoNombre = $proyecto->Nombre;

        // 2) KPI superiores simulados contra datos reales:
        $totalInversiones = Inversion::where('Proyecto', $proyectoNombre)->count();
        $vidaProyectoDias = $proyecto->Fecha ? Carbon::parse($proyecto->Fecha)->diffInDays(now()) : 0;
        $numInversionistas = ProyectoUsuario::where('FK_ID_Proyecto', $proyectoId)->distinct('FK_ID_Usuario')->count('FK_ID_Usuario');

        // Tasa ajustada (última)
        $tasaAjustada = (float) (Tasa::orderByDesc('Id')->value('Tasa') ?? 0.0);

        // 3) Totales por tipo
        // Tipo: 1=Dinero, 2=Especie, 3=Industria
        $sumTipo = Inversion::where('Proyecto', $proyectoNombre)
            ->selectRaw('FK_ID_Tipo, SUM(Monto) as total')
            ->groupBy('FK_ID_Tipo')
            ->pluck('total','FK_ID_Tipo')
            ->all();

        $totalCapital   = (float) (($sumTipo[1] ?? 0) + ($sumTipo[2] ?? 0)); // Dinero + Especie
        $totalIndustria = (float) ($sumTipo[3] ?? 0);
        $totalAportes   = $totalCapital + $totalIndustria;

        // 4) Gráfico “tipo de aporte”
        $chartValor = [
            'labels' => ['Dinero','Especie','Industria'],
            'series' => [
                (float)($sumTipo[1] ?? 0),
                (float)($sumTipo[2] ?? 0),
                (float)($sumTipo[3] ?? 0),
            ],
        ];

        // 5) Tabla “Aportes en dinero” (tipo 1) con ajuste por tasa
        $dinero = Inversion::where('Proyecto', $proyectoNombre)
            ->where('FK_ID_Tipo', 1)
            ->orderBy('Fecha')
            ->get(['Fecha','Monto']);

        $dineroRows = [];
        $dineroTotalAjustado = 0.0;
        foreach ($dinero as $row) {
            $fecha = $row->Fecha ? Carbon::parse($row->Fecha) : null;
            $dias  = $fecha ? $fecha->diffInDays(now()) : 0;
            // Ajuste anualizado simple (igual que el módulo anterior):
            $anios = $fecha ? now()->diffInYears($fecha) + (now()->diffInMonths($fecha)%12)/12 + (now()->diffInDaysFiltered(function(){return true;}, $fecha)%30)/365 : 0;
            $ajustado = $row->Monto * pow(1 + $tasaAjustada/100, max($anios, 0));

            $dineroRows[] = [
                'fecha'        => $fecha? $fecha->format('d/m/Y') : '',
                'monto'        => (float) $row->Monto,
                'ajustado'     => (float) $ajustado,
                'tiempo'       => $dias.' días',
            ];
            $dineroTotalAjustado += $ajustado;
        }

        // 6) Tabla “Aportes en especie” (tipo 2) — no hay subcolumnas en BD legacy
        $especie = Inversion::where('Proyecto', $proyectoNombre)
            ->where('FK_ID_Tipo', 2)
            ->orderBy('Fecha')
            ->get(['Fecha','Monto']);

        $especieRows = [];
        $especieTotalAjustado = 0.0;
        foreach ($especie as $row) {
            $fecha = $row->Fecha ? Carbon::parse($row->Fecha) : null;
            $dias  = $fecha ? $fecha->diffInDays(now()) : 0;
            $anios = $fecha ? now()->diffInYears($fecha) + (now()->diffInMonths($fecha)%12)/12 + (now()->diffInDaysFiltered(function(){return true;}, $fecha)%30)/365 : 0;
            $ajustado = $row->Monto * pow(1 + $tasaAjustada/100, max($anios, 0));

            // En legacy tenías varias columnas de especie; aquí mostramos total de “especie”
            $especieRows[] = [
                'fecha'        => $fecha? $fecha->format('d/m/Y') : '',
                'total'        => (float) $row->Monto,
                'ajustado'     => (float) $ajustado,
                'tiempo'       => $dias.' días',
            ];
            $especieTotalAjustado += $ajustado;
        }

        return view('inversionista.resumen', [
            'error'               => null,
            'proyecto'            => $proyecto,
            'kpis' => [
                'inversiones' => $totalInversiones,
                'vida_dias'   => $vidaProyectoDias,
                'personas'    => $numInversionistas,
                'tasa'        => $tasaAjustada,
            ],
            'totales' => [
                'capital'   => $totalCapital,
                'industria' => $totalIndustria,
                'aportes'   => $totalAportes,
            ],
            'chartValor'          => $chartValor,
            'dineroRows'          => $dineroRows,
            'dineroTotalAjustado' => $dineroTotalAjustado,
            'especieRows'         => $especieRows,
            'especieTotalAjustado'=> $especieTotalAjustado,
        ]);
    }
}

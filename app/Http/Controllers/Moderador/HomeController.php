<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Inversion2;   // tabla legacy de inversiones con certificado
use App\Models\Tipo;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // protege el dashboard
    }

    /** Resuelve proyecto activo por sesión y completa nombre si falta */
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

    public function index(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        // Tasa ajustada (último registro en tabla tasa)
        $tasaAjustada = DB::table('tasa')->orderByDesc('Id')->value('Tasa') ?? 0.0;

        // Lista de proyectos para selector oculto (opcional)
        $proyectos = Proyecto::orderBy('Nombre')->get(['ID_Proyecto','Nombre','Fecha','Descripcion','Certificado']);

        // ===== Resumen por usuario (Capital/Industria, % participación) =====
        $inversionesPorUsuario = [];
        $valorCapital = 0.0;
        $valorIndustria = 0.0;

        if ($proyectoId) {
            // Nota: en inversion2 se guarda NOMBRE del proyecto
            $rows = DB::table('inversion2 as i')
                ->join('proyecto as p', 'i.Proyecto', '=', 'p.Nombre')
                ->where('p.ID_Proyecto', $proyectoId)
                ->select('i.Nombre', 'i.Monto', 'i.FK_ID_Tipo', 'i.Fecha')
                ->get();

            foreach ($rows as $r) {
                $nombre = $r->Nombre;
                $monto = (float)$r->Monto;

                // años transcurridos
                $fechaInv = Carbon::parse($r->Fecha);
                $anios = now()->diffInDays($fechaInv) / 365.0;

                // valor futuro con tasa ajustada (porcentaje)
                $vf = $monto * pow(1 + ($tasaAjustada / 100), $anios);

                if (!isset($inversionesPorUsuario[$nombre])) {
                    $inversionesPorUsuario[$nombre] = [
                        'Nombre'    => $nombre,
                        'Capital'   => 0.0,
                        'Industria' => 0.0,
                        'Porcentaje'=> 0.0,
                    ];
                }

                // Tipos: 1/2 capital, 3 industria (según tu lógica)
                if (in_array((int)$r->FK_ID_Tipo, [1,2], true)) {
                    $inversionesPorUsuario[$nombre]['Capital'] += $vf;
                    $valorCapital += $vf;
                } elseif ((int)$r->FK_ID_Tipo === 3) {
                    $inversionesPorUsuario[$nombre]['Industria'] += $vf;
                    $valorIndustria += $vf;
                }
            }
        }

        $totalAportes = $valorCapital + $valorIndustria;
        $participacionMin = 0.0;
        $participacionMax = 0.0;
        $promedioParticipacion = 0.0;

        if ($totalAportes > 0) {
            foreach ($inversionesPorUsuario as $k => $v) {
                $totalU = $v['Capital'] + $v['Industria'];
                $inversionesPorUsuario[$k]['Porcentaje'] = ($totalU / $totalAportes) * 100.0;
            }
            $participaciones = array_column($inversionesPorUsuario, 'Porcentaje');
            $participacionMin = min($participaciones);
            $participacionMax = max($participaciones);
            $promedioParticipacion = array_sum($participaciones) / max(1, count($participaciones));
        }

        // ===== KPIs: cantidad usuarios, montos por tipo =====
        $cantidadUsuarios = 0;
        if ($proyectoId) {
            $cantidadUsuarios = (int) DB::table('proyecto_usuario')
                ->where('FK_ID_Proyecto', $proyectoId)
                ->count('FK_ID_Usuario');
        }

        // Montos por tipo en inversion2 filtrando por NOMBRE de proyecto
        $montoTipo1 = 0.0; // Dinero
        $montoTipo2 = 0.0; // Especie
        $montoTipo3 = 0.0; // Industria
        if ($proyectoNombre) {
            $montoTipo1 = (float) DB::table('inversion2')
                ->where('FK_ID_Tipo', 1)->where('Proyecto', $proyectoNombre)
                ->sum('Monto');
            $montoTipo2 = (float) DB::table('inversion2')
                ->where('FK_ID_Tipo', 2)->where('Proyecto', $proyectoNombre)
                ->sum('Monto');
            $montoTipo3 = (float) DB::table('inversion2')
                ->where('FK_ID_Tipo', 3)->where('Proyecto', $proyectoNombre)
                ->sum('Monto');
        }

        // ===== Datos mensuales (12 posiciones) por tipo (capital vs industria) =====
        $datosMensuales = [
            'capital'   => array_fill(0, 12, 0.0),
            'industria' => array_fill(0, 12, 0.0),
        ];
        if ($proyectoNombre) {
            // Agrupa por Mes/Anio/Tipo
            $mensuales = DB::table('inversion2')
                ->selectRaw('MONTH(Fecha) as Mes, YEAR(Fecha) as Anio, FK_ID_Tipo, SUM(Monto) as TotalMonto')
                ->where('Proyecto', $proyectoNombre)
                ->groupBy('Anio', 'Mes', 'FK_ID_Tipo')
                ->orderBy('Anio')->orderBy('Mes')
                ->get();

            foreach ($mensuales as $row) {
                $mesIndex = ((int)$row->Mes) - 1; // 0..11
                $tipoKey = in_array((int)$row->FK_ID_Tipo, [1,2], true) ? 'capital' : 'industria';
                if ($mesIndex >= 0 && $mesIndex < 12) {
                    $datosMensuales[$tipoKey][$mesIndex] += (float)$row->TotalMonto;
                }
            }
        }

        // Para selector (si quisieras permitir cambiar proyecto desde home)
        $proyectoSeleccionado = $proyectoId;

        return view('moderador.inicio', [
            'proyectoNombre'         => $proyectoNombre,
            'proyectos'              => $proyectos,
            'proyectoSeleccionado'   => $proyectoSeleccionado,
            'tasaAjustada'           => $tasaAjustada,

            'inversionesPorUsuario'  => $inversionesPorUsuario,
            'valorCapital'           => $valorCapital,
            'valorIndustria'         => $valorIndustria,
            'totalAportes'           => $totalAportes,

            'participacionMin'       => $participacionMin,
            'participacionMax'       => $participacionMax,
            'promedioParticipacion'  => $promedioParticipacion,

            'cantidadUsuarios'       => $cantidadUsuarios,
            'montoTipo1'             => $montoTipo1,
            'montoTipo2'             => $montoTipo2,
            'montoTipo3'             => $montoTipo3,

            'datosMensuales'         => $datosMensuales,
        ]);
    }
}

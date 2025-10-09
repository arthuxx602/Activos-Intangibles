<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Usuario;
use App\Models\Proyecto;
use App\Models\Inversion;
use App\Models\Tasa;

class ConsultaController extends Controller
{
    /**
     * GET /api/consultas/usuarios
     * Lista de usuarios (excluye rol 1)
     */
    public function usuarios()
    {
        return Usuario::query()
            ->where('FK_ID_Rol', '!=', 1)
            ->get(['ID_Usuario', 'Nombre', 'Apellido']);
    }

    /**
     * GET /api/consultas/proyectos-por-usuario/{usuario}
     * Proyectos asociados al usuario (para el select dependiente)
     */
    public function proyectosPorUsuario(Usuario $usuario)
    {
        $proyectos = DB::table('proyecto as p')
            ->join('proyecto_usuario as pu', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->where('pu.FK_ID_Usuario', $usuario->ID_Usuario)
            ->orderBy('p.Nombre')
            ->get(['p.ID_Proyecto', 'p.Nombre']);

        return $proyectos;
    }

    /**
     * GET /api/consultas/resumen?usuario=..&proyecto=..
     * Métricas + detalle y totales ajustados
     */
    public function resumen(Request $r)
    {
        $data = $r->validate([
            'usuario'  => 'required|integer|exists:usuario2,ID_Usuario',
            'proyecto' => 'nullable|integer|exists:proyecto,ID_Proyecto',
        ]);

        $usuarioId  = (int) $data['usuario'];
        $proyectoId = $data['proyecto'] ?? null;

        // 1) Número de inversiones realizadas por el usuario
        $numInversiones = Inversion::where('FK_ID_Usuario', $usuarioId)->count();

        // 2) Vida del proyecto (días)
        $diasVida = 0;
        if ($proyectoId) {
            $fechaInicio = Proyecto::where('ID_Proyecto', $proyectoId)->value('Fecha');
            if ($fechaInicio) {
                $diasVida = Carbon::parse($fechaInicio)->diffInDays(Carbon::now());
            }
        }

        // 3) Número de inversionistas del proyecto
        $numInversionistas = 0;
        if ($proyectoId) {
            $numInversionistas = DB::table('proyecto_usuario')
                ->where('FK_ID_Proyecto', $proyectoId)
                ->count();
        }

        // 4) Tasa ajustada (último registro)
        $tasaAjustada = (float) (Tasa::orderByDesc('Id')->value('Tasa') ?? 0);

        // 5) Detalles por tipo: 1=dinero, 2=especie, 3=industria
        $hoy = Carbon::today();

        $detalleTipo = function (int $tipo) use ($usuarioId, $hoy, $tasaAjustada) {
            $rows = Inversion::query()
                ->where('FK_ID_Usuario', $usuarioId)
                ->where('FK_ID_Tipo', $tipo)
                ->orderBy('Fecha')
                ->get(['ID_Inversion','Fecha','Monto']);

            $items = [];
            $total = 0;

            foreach ($rows as $row) {
                $fecha = Carbon::parse($row->Fecha);
                $dias  = $fecha->diffInDays($hoy);
                $valor = round($row->Monto * pow(1 + ($tasaAjustada/100), $dias/365), 0);

                $items[] = [
                    'id'                 => $row->ID_Inversion,
                    'fecha'              => $fecha->toDateString(),
                    'monto'              => (float) $row->Monto,
                    'dias_transcurridos' => $dias,
                    'valor_ajustado'     => (float) $valor,
                ];
                $total += $valor;
            }

            return ['items' => $items, 'total' => (float) round($total, 0)];
        };

        $dinero    = $detalleTipo(1);
        $especie   = $detalleTipo(2);
        $industria = $detalleTipo(3);

        $capital = round($dinero['total'] + $especie['total'], 0);
        $total   = round($capital + $industria['total'], 0);

        return response()->json([
            'metricas' => [
                'num_inversiones_realizadas' => $numInversiones,
                'vida_proyecto_dias'         => $diasVida,
                'num_inversionistas'         => $numInversionistas,
                'tasa_ajustada'              => $tasaAjustada,
            ],
            'resumen' => [
                'aportes_dinero_total'    => $dinero['total'],
                'aportes_especie_total'   => $especie['total'],
                'aportes_industria_total' => $industria['total'],
                'aportes_capital'         => $capital,
                'total_aportes'           => $total,
            ],
            'detalle' => [
                'dinero'    => $dinero['items'],
                'especie'   => $especie['items'],
                'industria' => $industria['items'],
            ],
        ]);
    }

    /**
     * (Opcional) Buscador genérico
     * GET /api/consultas/busqueda?q=texto&monto_min=0
     */
    public function busqueda(Request $r)
    {
        $term = $r->get('q');

        $proyectos = Proyecto::where('Nombre','like',"%{$term}%")
            ->limit(10)->get(['ID_Proyecto as id','Nombre']);

        // Si no tienes tabla empresas, comenta este bloque:
        $empresas = DB::table('empresas')
            ->where('Nombre','like',"%{$term}%")
            ->limit(10)->get(['id','Nombre']);

        $inversiones = Inversion::where('Monto','>=', (float)($r->get('monto_min', 0)))
            ->limit(10)->get(['ID_Inversion as id','Monto']);

        return compact('proyectos','empresas','inversiones');
    }
}
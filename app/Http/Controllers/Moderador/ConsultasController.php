<?php

namespace App\Http\Controllers\Moderador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use App\Models\Proyecto;
use App\Models\Usuario;
use App\Models\Inversion2;

class ConsultasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Resuelve proyecto activo con nombre (como en tus otros módulos) */
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

    /** GET: carga el form con la lista de usuarios del proyecto activo */
    public function index(Request $request)
    {
        [$proyectoId, $proyectoNombre] = $this->resolveProyectoIdYNombre($request);

        // Usuarios vinculados al proyecto activo
        $usuarios = Usuario::query()
            ->join('proyecto_usuario as pu', 'usuario2.ID_Usuario', '=', 'pu.FK_ID_Usuario')
            ->where('pu.FK_ID_Proyecto', $proyectoId)
            ->orderBy('Nombre')
            ->select('usuario2.ID_Usuario','usuario2.Nombre','usuario2.Apellido')
            ->get();

        // Última tasa
        $tasaAjustada = (float) DB::table('tasa')->orderByDesc('Id')->value('Tasa') ?? 0.0;

        return view('moderador.consultas.index', [
            'proyectoId'     => $proyectoId,
            'proyectoNombre' => $proyectoNombre,
            'usuarios'       => $usuarios,
            'tasaAjustada'   => $tasaAjustada,

            // valores por defecto (sin consultar)
            'result'         => null,
        ]);
    }

    /** POST: procesa la consulta y arma métricas + tablas */
    public function consultar(Request $request)
    {
        [$proyectoIdSession, $proyectoNombreSession] = $this->resolveProyectoIdYNombre($request);

        $data = $request->validate([
            'usuario'  => ['required','integer'],
            'proyecto' => ['required','integer'], // ID_Proyecto
        ]);

        $usuarioId   = (int) $data['usuario'];
        $proyectoId  = (int) $data['proyecto'];

        $proyecto = Proyecto::where('ID_Proyecto', $proyectoId)->first(['ID_Proyecto','Nombre','Fecha']);
        if (!$proyecto) {
            return back()->withErrors('Proyecto no encontrado')->withInput();
        }
        $proyectoNombre = $proyecto->Nombre;

        // Última tasa
        $tasaAjustada = (float) DB::table('tasa')->orderByDesc('Id')->value('Tasa') ?? 0.0;

        // # inversiones del usuario en ese proyecto (filtrando por NOMBRE en inversion2)
        $numInversiones = (int) Inversion2::query()
            ->where('FK_ID_Usuario', $usuarioId)
            ->where('proyecto', $proyectoNombre)
            ->count();

        // Vida del proyecto (días)
        $vidaDias = 0;
        if ($proyecto->Fecha) {
            $vidaDias = now()->diffInDays(Carbon::parse($proyecto->Fecha));
        }

        // # inversionistas del proyecto
        $numInversionistas = (int) DB::table('proyecto_usuario')
            ->where('FK_ID_Proyecto', $proyectoId)
            ->count('FK_ID_Usuario');

        // Listas por tipo (1: dinero, 2: especie, 3: industria) con VF
        $tipos = [1 => 'dinero', 2 => 'especie', 3 => 'industria'];
        $tablas = [];
        $totalesVF = ['dinero' => 0.0, 'especie' => 0.0, 'industria' => 0.0];

        foreach ($tipos as $tipoId => $key) {
            $rows = Inversion2::query()
                ->where('FK_ID_Tipo', $tipoId)
                ->where('FK_ID_Usuario', $usuarioId)
                ->where('proyecto', $proyectoNombre)
                ->orderBy('Fecha')
                ->get(['Fecha','Monto']);

            $acum = 0.0;
            $items = [];
            foreach ($rows as $r) {
                $dias = now()->diffInDays(Carbon::parse($r->Fecha));
                $vf   = (float)$r->Monto * pow(1 + ($tasaAjustada/100), $dias / 365.0);
                $acum += $vf;

                $items[] = [
                    'Fecha'     => Carbon::parse($r->Fecha)->format('Y-m-d'),
                    'Monto'     => (float)$r->Monto,
                    'VF'        => $vf,
                    'Dias'      => $dias,
                    'Acumulado' => $acum, // para “Total aporte … (ajustado)” incremental como en legacy
                ];
            }

            $tablas[$key] = $items;
            $totalesVF[$key] = $acum;
        }

        // Resumen capital / industria / total
        $valorCapital   = $totalesVF['dinero'] + $totalesVF['especie'];
        $valorIndustria = $totalesVF['industria'];
        $totalAportes   = $valorCapital + $valorIndustria;

        // Para recargar el form: usuarios del proyecto activo (como GET)
        $usuarios = Usuario::query()
            ->join('proyecto_usuario as pu', 'usuario2.ID_Usuario', '=', 'pu.FK_ID_Usuario')
            ->where('pu.FK_ID_Proyecto', $proyectoIdSession)
            ->orderBy('Nombre')
            ->select('usuario2.ID_Usuario','usuario2.Nombre','usuario2.Apellido')
            ->get();

        return view('moderador.consultas.index', [
            'proyectoId'     => $proyectoIdSession,
            'proyectoNombre' => $proyectoNombreSession,
            'usuarios'       => $usuarios,
            'tasaAjustada'   => $tasaAjustada,
            'result' => [
                'usuarioId'          => $usuarioId,
                'proyectoId'         => $proyectoId,
                'proyectoNombre'     => $proyectoNombre,
                'numInversiones'     => $numInversiones,
                'vidaDias'           => $vidaDias,
                'numInversionistas'  => $numInversionistas,
                'tablas'             => $tablas,
                'totalesVF'          => $totalesVF,
                'valorCapital'       => $valorCapital,
                'valorIndustria'     => $valorIndustria,
                'totalAportes'       => $totalAportes,
            ],
        ]);
    }

    /** AJAX: devuelve <option>… de los proyectos del usuario (marca seleccionado si coincide con sesión) */
    public function proyectosPorUsuario(Request $request)
    {
        $usuarioId = (int) $request->query('usuario_id', 0);
        if (!$usuarioId) return response('<option value="">Seleccione un usuario</option>', 200);

        [$proyectoIdSession] = $this->resolveProyectoIdYNombre($request);

        $proyectos = DB::table('proyecto as p')
            ->join('proyecto_usuario as pu', 'p.ID_Proyecto', '=', 'pu.FK_ID_Proyecto')
            ->where('pu.FK_ID_Usuario', $usuarioId)
            ->orderBy('p.Nombre')
            ->select('p.ID_Proyecto','p.Nombre')
            ->get();

        $html = '<option value="" selected>Seleccione un proyecto</option>';
        foreach ($proyectos as $p) {
            $sel = ($p->ID_Proyecto == $proyectoIdSession) ? ' selected' : '';
            $html .= "<option value=\"{$p->ID_Proyecto}\"{$sel}>{$p->Nombre}</option>";
        }
        return response($html, 200)->header('Content-Type','text/html; charset=UTF-8');
    }
}

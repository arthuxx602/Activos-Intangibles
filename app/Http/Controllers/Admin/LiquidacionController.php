<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Proyecto;

class LiquidacionController extends Controller
{
    /**
     * GET /api/liquidaciones?proyecto=ID&fecha_corte=YYYY-MM-DD
     * Devuelve resumen de liquidación (sin persistir cambios).
     */
    public function index(Request $r)
    {
        $proyectoId = (int) $r->query('proyecto');
        if (!$proyectoId) {
            return response()->json(['message' => 'Parámetro proyecto es requerido.'], 422);
        }

        $fechaCorte = $r->filled('fecha_corte') ? Carbon::parse($r->query('fecha_corte')) : Carbon::today();

        // Proyecto
        $proyecto = DB::table('proyecto')->where('ID_Proyecto', $proyectoId)->first();
        if (!$proyecto) {
            return response()->json(['message' => 'Proyecto no encontrado.'], 404);
        }

        // Última tasa
        $tasaRow = DB::table('tasa')->orderByDesc('Id')->first();
        $tasa = $tasaRow ? (float) $tasaRow->Tasa : 0.0;
        $tasaDecimal = $tasa / 100.0;

        // Inversiones del proyecto
        $inversiones = DB::table('inversion2 as i')
            ->select(
                'i.ID_Inversion',
                'i.FK_ID_Usuario',
                'i.FK_ID_Proyecto',
                'i.FK_ID_Tipo',
                'i.Monto',
                'i.Fecha',
                'i.Descripcion',
                'u.Nombre as NombreUsuario',
                'u.Apellido as ApellidoUsuario'
            )
            ->join('usuario2 as u', 'u.ID_Usuario', '=', 'i.FK_ID_Usuario')
            ->where('i.FK_ID_Proyecto', $proyectoId)
            ->orderBy('i.Fecha')
            ->get();

        // Agrupar y calcular
        $usuarios = [];              // por usuario: totales por tipo + total
        $totalesPorTipo = [1 => 0, 2 => 0, 3 => 0];  // dinero, especie, industria
        $totalAjustadoGlobal = 0;

        foreach ($inversiones as $inv) {
            $fechaInv = Carbon::parse($inv->Fecha);
            $dias = max(0, $fechaInv->diffInDays($fechaCorte)); // nunca negativo
            $valorFuturo = round($inv->Monto * pow(1 + $tasaDecimal, $dias / 365), 0);

            $uid = $inv->FK_ID_Usuario;
            if (!isset($usuarios[$uid])) {
                $usuarios[$uid] = [
                    'usuario_id' => $uid,
                    'nombre'     => trim(($inv->NombreUsuario ?? '').' '.($inv->ApellidoUsuario ?? '')),
                    'dinero'     => 0,
                    'especie'    => 0,
                    'industria'  => 0,
                    'total'      => 0,
                    'detalles'   => [] // lista de inversiones del usuario
                ];
            }

            // Mapear tipo a clave legible
            $claveTipo = match((int)$inv->FK_ID_Tipo) {
                1 => 'dinero',
                2 => 'especie',
                3 => 'industria',
                default => 'otros',
            };

            $usuarios[$uid][$claveTipo] += $valorFuturo;
            $usuarios[$uid]['total']    += $valorFuturo;

            $totalesPorTipo[(int)$inv->FK_ID_Tipo] += $valorFuturo;
            $totalAjustadoGlobal += $valorFuturo;

            $usuarios[$uid]['detalles'][] = [
                'id'           => $inv->ID_Inversion,
                'tipo'         => (int)$inv->FK_ID_Tipo,
                'monto'        => (float)$inv->Monto,
                'fecha'        => (string)$inv->Fecha,
                'dias'         => $dias,
                'valor_futuro' => $valorFuturo,
                'descripcion'  => $inv->Descripcion,
            ];
        }

        // Vida del proyecto (desde Fecha proyecto a fecha corte)
        $fechaProyecto = $proyecto->Fecha ? Carbon::parse($proyecto->Fecha) : null;
        $vidaDias = $fechaProyecto ? $fechaProyecto->diffInDays($fechaCorte) : 0;

        // Resumen de respuesta
        return response()->json([
            'proyecto' => [
                'id'         => $proyecto->ID_Proyecto,
                'nombre'     => $proyecto->Nombre,
                'fecha'      => $proyecto->Fecha,
                'liquidado'  => (int) ($proyecto->liquidado ?? 0),
            ],
            'tasa' => [
                'valor' => $tasa,           // %
                'fuente' => $tasaRow?->Id,
            ],
            'fecha_corte' => $fechaCorte->toDateString(),
            'vida_proyecto_dias' => $vidaDias,
            'totales' => [
                'dinero'    => $totalesPorTipo[1] ?? 0,
                'especie'   => $totalesPorTipo[2] ?? 0,
                'industria' => $totalesPorTipo[3] ?? 0,
                'total'     => $totalAjustadoGlobal,
            ],
            // lista de usuarios con totales y detalles de inversiones
            'usuarios' => array_values($usuarios),
        ]);
    }

    /**
     * POST /api/liquidaciones
     * (Opcional) guardaría un registro de liquidación; en tu app ya definiste
     * ProyectoController@liquidar, así que delegamos allí para marcar el proyecto como liquidado.
     */
    public function store(Request $r)
    {
        $proyectoId = (int) $r->input('proyecto');
        if (!$proyectoId) {
            return response()->json(['message' => 'Parámetro proyecto es requerido.'], 422);
        }

        // Redirige a la acción de ProyectoController si ya la tienes implementada.
        // Aquí devolvemos 501 para que uses /api/proyectos/{proyecto}/liquidar
        return response()->json([
            'message' => 'Usa POST /api/proyectos/{proyecto}/liquidar para confirmar la liquidación.',
        ], 501);
    }
    /**
 * POST /api/proyectos/{proyecto}/liquidar
 * Request: form-data con campo obligatorio 'documento_L' (pdf/zip/jpg/png, máx 10MB)
 * Efecto: sube el acta, guarda el nombre en Certificado_L y marca liquidado = 1
 */
public function liquidar(Request $r, Proyecto $proyecto)
{
    // Validación de archivo
    $data = $r->validate([
        'documento_L' => 'required|file|mimes:pdf,zip,jpg,jpeg,png|max:10240', // 10MB
    ]);

    // Bloquear si ya está liquidado
    if ((int)($proyecto->liquidado ?? 0) === 1) {
        return response()->json([
            'message' => 'El proyecto ya se encuentra liquidado.',
        ], 409);
    }

    // Guardar archivo en storage/app/certificados/liquidaciones
    $file = $r->file('documento_L');
    $dir  = storage_path('app/certificados/liquidaciones');
    if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

    $base = 'liquidacion-proyecto-'.$proyecto->ID_Proyecto.'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
    $ext  = strtolower($file->getClientOriginalExtension());
    $name = $base.'.'.$ext;

    $i = 1;
    while (file_exists($dir.DIRECTORY_SEPARATOR.$name)) {
        $name = $base.'-'.$i.'.'.$ext;
        $i++;
    }

    $file->move($dir, $name);

    // Actualizar proyecto
    $proyecto->liquidado    = 1;
    $proyecto->Certificado_L = $name;              // guardamos solo el nombre del archivo (no ruta)
    // Si tienes una columna para fecha de liquidación, descomenta:
    // $proyecto->Fecha_Liquidacion = now()->toDateString();
    $proyecto->save();

    return response()->json([
        'message'  => 'Liquidación registrada exitosamente.',
        'proyecto' => $proyecto->fresh(),
        'archivo'  => $name,
    ], 200);
}
}

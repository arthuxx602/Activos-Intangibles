<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    // datosLine.php -> series para gráficas
    public function datosLine(Request $r)
    {
        $series = DB::table('proyectos')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as periodo, SUM(monto) as total')
            ->groupBy('periodo')->orderBy('periodo')
            ->get();

        return response()->json($series);
    }
}

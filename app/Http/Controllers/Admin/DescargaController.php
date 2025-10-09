<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inversion;
use App\Models\Proyecto;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DescargaController extends Controller
{
    public function inversiones(): StreamedResponse
    {
        $rows = Inversion::with('proyecto:id,nombre')->cursor();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=inversiones.csv'
        ];

        return response()->stream(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Proyecto','Monto','Fecha']);
            foreach ($rows as $i) {
                fputcsv($out, [$i->id, optional($i->proyecto)->nombre, $i->monto, $i->created_at]);
            }
            fclose($out);
        }, 200, $headers);
    }

    public function proyectos(): StreamedResponse
    {
        $rows = Proyecto::cursor();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=proyectos.csv'
        ];

        return response()->stream(function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID','Nombre','Monto','Estado','Creacion']);
            foreach ($rows as $p) {
                fputcsv($out, [$p->id, $p->nombre, $p->monto, $p->estado, $p->created_at]);
            }
            fclose($out);
        }, 200, $headers);
    }
}

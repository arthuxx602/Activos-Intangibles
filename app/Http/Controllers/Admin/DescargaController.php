<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inversion;
use App\Models\Proyecto;

class DescargaController extends Controller
{
    /**
     * GET /api/descargas/{recurso}/{id}/certificado
     *  - recurso: inversiones | proyectos
     *  - id: ID del registro
     */
    public function certificado(string $recurso, int $id)
    {
        $base = storage_path('app/certificados');

        switch ($recurso) {
            case 'inversiones': {
                $inv = Inversion::findOrFail($id);
                $filename = $inv->CertificadoInversion; // nombre guardado en DB (legacy)
                $path = $base . DIRECTORY_SEPARATOR . 'inversiones' . DIRECTORY_SEPARATOR . $filename;
                $downloadName = $filename ?: ("certificado-inversion-{$id}.pdf");
                break;
            }
            case 'proyectos': {
                $proy = Proyecto::findOrFail($id);
                $filename = $proy->Certificado; // nombre guardado en DB (legacy)
                $path = $base . DIRECTORY_SEPARATOR . 'proyectos' . DIRECTORY_SEPARATOR . $filename;
                $downloadName = $filename ?: ("certificado-proyecto-{$id}.pdf");
                break;
            }
            // app/Http/Controllers/Admin/DescargaController.php
// ... dentro de certificado($recurso, $id)
case 'proyectos-liquidacion': {
    $proy = Proyecto::findOrFail($id);
    $filename = $proy->Certificado_L; // acta de liquidación
    $path = storage_path('app/certificados/liquidaciones/' . $filename);
    $downloadName = $filename ?: ("acta-liquidacion-proyecto-{$id}.pdf");
    break;
}

            default:
                return response()->json(['message' => 'Recurso no soportado.'], 404);
        }

        if (!$filename) {
            return response()->json(['message' => 'Este registro no tiene certificado asociado.'], 404);
        }

        if (!is_file($path)) {
            return response()->json(['message' => 'Archivo no encontrado en el servidor.'], 404);
        }

        // Detecta MIME en servidor (fallback genérico)
        $mime = function_exists('mime_content_type') ? (mime_content_type($path) ?: 'application/octet-stream') : 'application/octet-stream';

        return response()->download($path, $downloadName, [
            'Content-Type' => $mime,
        ]);
    }

    /**
     * (Opcional) GET /api/descargas/inversiones
     * Lista inversiones con certificado y URL de descarga.
     */
    public function inversiones()
    {
        $items = Inversion::whereNotNull('CertificadoInversion')
            ->orderByDesc('ID_Inversion')
            ->get(['ID_Inversion', 'Nombre', 'CertificadoInversion']);

        return $items->map(fn ($i) => [
            'id'           => $i->ID_Inversion,
            'nombre'       => $i->Nombre,
            'archivo'      => $i->CertificadoInversion,
            'download_url' => url("/api/descargas/inversiones/{$i->ID_Inversion}/certificado"),
        ]);
    }

    /**
     * (Opcional) GET /api/descargas/proyectos
     * Lista proyectos con certificado y URL de descarga.
     */
    public function proyectos()
    {
        $items = Proyecto::whereNotNull('Certificado')
            ->orderByDesc('ID_Proyecto')
            ->get(['ID_Proyecto', 'Nombre', 'Certificado']);

        return $items->map(fn ($p) => [
            'id'           => $p->ID_Proyecto,
            'nombre'       => $p->Nombre,
            'archivo'      => $p->Certificado,
            'download_url' => url("/api/descargas/proyectos/{$p->ID_Proyecto}/certificado"),
        ]);
    }
}

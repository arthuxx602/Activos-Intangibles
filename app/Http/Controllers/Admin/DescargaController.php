<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inversion;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DescargaController extends Controller
{
    /**
     * GET /api/descargas/{recurso}/{id}/certificado
     * {recurso} = inversiones | proyectos
     */
    public function certificado(string $recurso, int $id)
    {
        // 1) Resolver modelo y campo archivo según recurso
        switch ($recurso) {
            case 'inversiones':
                $model = Inversion::findOrFail($id);
                $filename = basename((string) $model->CertificadoInversion); // campo en BD
                $candidatePaths = [
                    // Storage (recomendado)
                    storage_path('app/certificados/inversiones/'.$filename),
                    // Public (fallback)
                    public_path('certificados/inversiones/'.$filename),
                ];
                break;

            case 'proyectos':
                $model = Proyecto::findOrFail($id);
                $filename = basename((string) $model->Certificado); // campo en BD
                $candidatePaths = [
                    // Storage (recomendado)
                    storage_path('app/certificados/proyectos/'.$filename),
                    // Fallbacks comunes
                    public_path('certificados/proyectos/'.$filename),
                    public_path('Documento_P/'.$filename), // compat con tu legacy
                ];
                break;

            default:
                return response()->json(['message' => 'Recurso no soportado.'], 400);
        }

        // 2) Validaciones básicas
        if ($filename === '' || $filename === '.' || $filename === '..') {
            return response()->json(['message' => 'Archivo no configurado.'], 404);
        }

        // 3) Buscar primer path existente y descargar
        foreach ($candidatePaths as $path) {
            if ($path && is_file($path)) {
                // entrega como adjunto
                return response()->download($path, $filename);
            }
        }

        // 4) No encontrado
        return response()->json(['message' => 'Archivo no encontrado.'], 404);
    }
}

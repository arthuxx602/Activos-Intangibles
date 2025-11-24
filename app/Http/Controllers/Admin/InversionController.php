<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Inversion; 
use App\Models\moderador\Inversion2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

class InversionController extends Controller
{
    /**
     * WEB: Devuelve la vista Blade de inversiones
     */
    public function indexView()
    {
        return view('inversiones.index'); // Asegúrate de que exista resources/views/inversiones/index.blade.php
    }

    /**
     * API: Devuelve JSON de inversiones con filtros
     */
    public function index(Request $r)
    {
        $q = Inversion2::query()
            ->with([
                'usuario:ID_Usuario,Nombre,Apellido',
                'proyecto:ID_Proyecto,Nombre',
                'tipo:ID_TIPO,Nombre'
            ]);

        if ($r->filled('usuario'))   $q->where('FK_ID_Usuario',  $r->usuario);
        if ($r->filled('proyecto'))  $q->where('FK_ID_Proyecto', $r->proyecto);
        if ($r->filled('tipo'))      $q->where('FK_ID_Tipo',     $r->tipo);
        if ($r->filled('from'))      $q->whereDate('Fecha', '>=', $r->date('from'));
        if ($r->filled('to'))        $q->whereDate('Fecha', '<=', $r->date('to'));
        if ($r->filled('search')) {
            $s = $r->input('search');
            $q->where(function ($q) use ($s) {
                $q->where('Nombre', 'like', "%$s%")
                    ->orWhere('Descripcion', 'like', "%$s%");
            });
        }

        return $q->orderByDesc('Fecha')->paginate(20);
    }

    /**
     * Crear inversión (API)
            'Nombre'          => 'required|string|max:200',
            'Monto'           => 'required|numeric|min:0',
            'Fecha'           => 'required|date',
            'Descripcion'     => 'nullable|string',
            'FK_ID_Usuario'   => 'required|integer|exists:usuario2,ID_Usuario',
            'FK_ID_Proyecto'  => 'required|integer|exists:proyecto,ID_Proyecto',
            'FK_ID_Tipo'      => 'required|integer|exists:tipo,ID_TIPO',
            'CertificadoInversion' => 'nullable|file|mimes:pdf,zip,jpg,jpeg,png|max:10240',
        ]);

        $inv = new Inversion($data);

        if ($r->hasFile('CertificadoInversion')) {
            $file = $r->file('CertificadoInversion');
            $inv->CertificadoInversion = $this->storeCert($file, $data['FK_ID_Usuario'], $data['FK_ID_Proyecto']);
        }

        $inv->save();

        return response()->json([
            'message' => 'Inversión creada correctamente.',
            'data'    => $inv->load(['usuario', 'proyecto', 'tipo']),
        ], 201);
    }

    /**
     * Mostrar inversión específica (API)
     */
    // En este caso para saber donde va la realcion en si y saber que modelos estan tronando 
    public function show(Inversion2 $inversion, string $idExtra = null)
{
    // relaciones que nos gustaría tener
    $posibles = ['usuario', 'proyecto', 'tipo'];

    // nos quedamos SOLO con las que realmente existen como método relación
    $rels = collect($posibles)
        ->filter(function ($rel) use ($inversion) {
            // ¿el método existe en el modelo?
            if (!method_exists($inversion, $rel)) {
                return false;
            }
            // ¿ese método devuelve una relación Eloquent?
            return $inversion->{$rel}() instanceof Relation;
        })
        ->all();

    // cargamos solo las relaciones válidas
    $inversion->load($rels);

    // retornamos la inversión lista (esto puede ser JSON si esta ruta es API)
    return $inversion;
}
    
    /*public function show(Inversion2 $inversion)
    {
        return $inversion->load(['usuario', 'proyecto', 'tipo']);
    }*/

    /**
     * Actualizar inversión (API)
     */
    public function update(Request $r, Inversion2 $inversion)
    {
        $data = $r->validate([
            'Nombre'          => 'sometimes|required|string|max:200',
            'Monto'           => 'sometimes|required|numeric|min:0',
            'Fecha'           => 'sometimes|required|date',
            'Descripcion'     => 'sometimes|nullable|string',
            'FK_ID_Usuario'   => 'sometimes|required|integer|exists:usuario2,ID_Usuario',
            'FK_ID_Proyecto'  => 'sometimes|required|integer|exists:proyecto,ID_Proyecto',
            'FK_ID_Tipo'      => 'sometimes|required|integer|exists:tipo,ID_TIPO',
            'CertificadoInversion' => 'sometimes|file|mimes:pdf,zip,jpg,jpeg,png|max:10240',
        ]);

        $inversion->fill(collect($data)->except('CertificadoInversion')->all());

        if ($r->hasFile('CertificadoInversion')) {
            $file = $r->file('CertificadoInversion');
            $inversion->CertificadoInversion = $this->storeCert(
                $file,
                $inversion->FK_ID_Usuario ?? ($data['FK_ID_Usuario'] ?? 0),
                $inversion->FK_ID_Proyecto ?? ($data['FK_ID_Proyecto'] ?? 0)
            );
        }

        $inversion->save();

        return response()->json([
            'message' => 'Inversión actualizada.',
            'data'    => $inversion->fresh()->load(['usuario', 'proyecto', 'tipo']),
        ]);
    }

    /**
     * Eliminar inversión (API)
     */
    public function destroy(Inversion2 $inversion)
    {
        $id = $inversion->ID_Inversion;
        $inversion->delete();

        return response()->json(['message' => 'Inversión eliminada.', 'id' => $id]);
    }

    /**
     * Eliminar inversiones en lote (API)
     */
    public function destroyMany(Request $r)
    {
        $data = $r->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:inversion2,ID_Inversion',
        ]);

        DB::table('inversion2')->whereIn('ID_Inversion', $data['ids'])->delete();

        return response()->json([
            'message' => 'Inversiones eliminadas.',
            'ids'     => $data['ids'],
        ]);
    }

    /**
     * Guardar archivo CertificadoInversion
     */
    private function storeCert(\Illuminate\Http\UploadedFile $file, int $usuarioId, int $proyectoId): string
    {
        $dir = storage_path('app/certificados/inversiones');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }   

        $base = 'inv-u' . $usuarioId . '-p' . $proyectoId . '-' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext  = strtolower($file->getClientOriginalExtension());
        $name = $base . '.' . $ext;

        $i = 1;
        while (file_exists($dir . DIRECTORY_SEPARATOR . $name)) {
            $name = $base . '-' . $i . '.' . $ext;
            $i++;
        }

        $file->move($dir, $name);
        return $name;
    }
}
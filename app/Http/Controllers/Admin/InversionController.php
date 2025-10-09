<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InversionController extends Controller
{
    /**
     * GET /api/inversiones
     * Filtros: ?usuario=ID & proyecto=ID & tipo=ID & from=YYYY-MM-DD & to=YYYY-MM-DD & search=...
     */
    public function index(Request $r)
    {
        $q = Inversion::query()
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
     * POST /api/inversiones
     * Acepta archivo opcional 'CertificadoInversion'
     */
    public function store(Request $r)
    {
        $data = $r->validate([
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
            'data'    => $inv->load(['usuario','proyecto','tipo']),
        ], 201);
    }

    /**
     * GET /api/inversiones/{inversion}
     */
    public function show(Inversion $inversion)
    {
        return $inversion->load(['usuario','proyecto','tipo']);
    }

    /**
     * PUT /api/inversiones/{inversion}
     */
    public function update(Request $r, Inversion $inversion)
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
            'data'    => $inversion->fresh()->load(['usuario','proyecto','tipo']),
        ]);
    }

    /**
     * DELETE /api/inversiones/{inversion}
     */
    public function destroy(Inversion $inversion)
    {
        $id = $inversion->ID_Inversion;
        $inversion->delete();

        return response()->json(['message' => 'Inversión eliminada.', 'id' => $id]);
    }

    /**
     * DELETE /api/inversiones (lote)
     * Body: { "ids": [ ... ] }
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
     * Guarda CertificadoInversion en storage/app/certificados/inversiones
     */
    private function storeCert(\Illuminate\Http\UploadedFile $file, int $usuarioId, int $proyectoId): string
    {
        $dir = storage_path('app/certificados/inversiones');
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $base = 'inv-u'.$usuarioId.'-p'.$proyectoId.'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext  = strtolower($file->getClientOriginalExtension());
        $name = $base.'.'.$ext;

        $i = 1;
        while (file_exists($dir.DIRECTORY_SEPARATOR.$name)) {
            $name = $base.'-'.$i.'.'.$ext;
            $i++;
        }

        $file->move($dir, $name);
        return $name;
    }
}

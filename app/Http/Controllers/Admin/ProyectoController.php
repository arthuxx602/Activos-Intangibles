<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProyectoController extends Controller
{
    /**
     * GET /api/proyectos
     * Filtros: ?solo_no_liquidados=1&search=...&from=YYYY-MM-DD&to=YYYY-MM-DD
     * Orden:   ?order_by=ID_Proyecto|Nombre|Fecha&order_dir=asc|desc
     * Pagin.:  ?per_page=10..100
     */
    public function index(Request $r)
    {
        // Validar query params opcionales (seguros)
        $validated = $r->validate([
            'solo_no_liquidados' => 'sometimes|boolean',
            'search'             => 'sometimes|string|max:200',
            'from'               => 'sometimes|date',
            'to'                 => 'sometimes|date',
            'order_by'           => 'sometimes|in:ID_Proyecto,Nombre,Fecha',
            'order_dir'          => 'sometimes|in:asc,desc',
            'per_page'           => 'sometimes|integer|min:1|max:100',
        ]);

        $q = Proyecto::query();

        if ($r->boolean('solo_no_liquidados')) {
            $q->where('liquidado', '<>', 1);
        }

        if ($r->filled('search')) {
            $q->where('Nombre', 'like', '%'.$r->input('search').'%');
        }

        if ($r->filled('from')) {
            $q->whereDate('Fecha', '>=', $r->date('from'));
        }

        if ($r->filled('to')) {
            $q->whereDate('Fecha', '<=', $r->date('to'));
        }

        $orderBy  = $validated['order_by']  ?? 'ID_Proyecto';
        $orderDir = $validated['order_dir'] ?? 'desc';
        $perPage  = $validated['per_page']  ?? 20;

        return $q->orderBy($orderBy, $orderDir)->paginate($perPage);
    }

    /**
     * GET /api/proyectos/{proyecto}
     */
    public function show(Proyecto $proyecto)
    {
        return $proyecto;
    }

    /**
     * POST /api/proyectos
     * Crea un proyecto (acepta archivo 'Certificado' opcional).
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'Nombre'      => 'required|string|max:200',
            'Fecha'       => 'nullable|date',
            'Descripcion' => 'nullable|string',
            'Certificado' => 'nullable|file|mimes:pdf,zip,jpg,jpeg,png|max:10240', // 10MB
        ]);

        $proyecto = new Proyecto();
        $proyecto->Nombre      = $data['Nombre'];
        $proyecto->Fecha       = $data['Fecha'] ?? null;
        $proyecto->Descripcion = $data['Descripcion'] ?? null;
        $proyecto->save(); // para obtener ID_Proyecto

        if ($r->hasFile('Certificado')) {
            $file = $r->file('Certificado');
            $nombreArchivo = $this->storeCertificado($file, $proyecto->ID_Proyecto);
            $proyecto->Certificado = $nombreArchivo; // guardamos sólo el nombre (compat legacy)
            $proyecto->save();
        }

        return response()->json([
            'message' => 'Proyecto creado exitosamente.',
            'data'    => $proyecto->fresh(),
        ], 201);
    }

    /**
     * PUT /api/proyectos/{proyecto}
     * Actualiza un proyecto (permite reemplazar el Certificado).
     */
    public function update(Request $r, Proyecto $proyecto)
    {
        $data = $r->validate([
            'Nombre'      => 'sometimes|required|string|max:200',
            'Fecha'       => 'sometimes|date',
            'Descripcion' => 'sometimes|nullable|string',
            'Certificado' => 'sometimes|file|mimes:pdf,zip,jpg,jpeg,png|max:10240', // 10MB
        ]);

        // Campos simples
        $proyecto->fill(collect($data)->except('Certificado')->all());

        // Archivo (opcional)
        if ($r->hasFile('Certificado')) {
            $file = $r->file('Certificado');
            $nuevoNombre = $this->storeCertificado($file, $proyecto->ID_Proyecto);
            $proyecto->Certificado = $nuevoNombre; // solo el nombre
        }

        $proyecto->save();

        return response()->json([
            'message' => 'Proyecto actualizado exitosamente.',
            'data'    => $proyecto->fresh(),
        ], 200);
    }

    /**
     * DELETE /api/proyectos/{proyecto}
     * Evita eliminar si el proyecto está vinculado a usuarios (proyecto_usuario).
     */
    public function destroy(Proyecto $proyecto)
    {
        $tieneAsociaciones = DB::table('proyecto_usuario')
            ->where('FK_ID_Proyecto', $proyecto->ID_Proyecto)
            ->exists();

        if ($tieneAsociaciones) {
            return response()->json([
                'message' => 'No se puede eliminar el proyecto porque tiene datos asociados (usuarios vinculados).'
            ], 409);
        }

        $id = $proyecto->ID_Proyecto;
        $proyecto->delete();

        return response()->json([
            'message' => 'Proyecto eliminado exitosamente.',
            'id'      => $id,
        ], 200);
    }

    /**
     * DELETE /api/proyectos
     * Elimina varios proyectos si no están asociados (body: { "ids": [..] }).
     */
    public function destroyMany(Request $request)
    {
        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer|exists:proyecto,ID_Proyecto',
        ]);

        $ids = $data['ids'];

        // Proyectos con asociaciones
        $bloqueados = DB::table('proyecto_usuario')
            ->whereIn('FK_ID_Proyecto', $ids)
            ->pluck('FK_ID_Proyecto')
            ->unique()
            ->map(fn($v) => (int)$v)
            ->all();

        // Proyectos sin asociaciones
        $eliminables = array_values(array_diff($ids, $bloqueados));

        if (!empty($eliminables)) {
            DB::table('proyecto')->whereIn('ID_Proyecto', $eliminables)->delete();
        }

        return response()->json([
            'eliminados' => $eliminables,
            'bloqueados' => $bloqueados,
            'message'    => 'Operación completada.',
        ]);
    }

    /**
     * Guarda el certificado en storage/app/certificados/proyectos
     * y devuelve el nombre de archivo seguro (sin sobreescribir).
     */
    private function storeCertificado(\Illuminate\Http\UploadedFile $file, int $idProyecto): string
    {
        $dir = storage_path('app/certificados/proyectos');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $base = 'proyecto-'.$idProyecto.'-'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
        $ext  = strtolower($file->getClientOriginalExtension());
        $name = $base.'.'.$ext;

        // Evitar sobrescritura
        $i = 1;
        while (file_exists($dir.DIRECTORY_SEPARATOR.$name)) {
            $name = $base.'-'.$i.'.'.$ext;
            $i++;
        }

        $file->move($dir, $name); // storage/app/certificados/proyectos

        return $name;
    }
}

public function datosLine(Request $r)
{
    // Parámetros opcionales
    $data = $r->validate([
        'proyecto'     => 'nullable|string',          // nombre del proyecto (como tu sesión)
        'proyecto_id'  => 'nullable|integer',         // si inversion2 tiene FK_ID_Proyecto
        'desde_anio'   => 'nullable|integer',         // default 2018
        'hasta_anio'   => 'nullable|integer',         // default 2030
    ]);

    // Compatibilidad con tu legacy: si no viene en query, intenta leer de sesión
    $nombreProyecto = $data['proyecto']
        ?? $r->session()->get('nombreProyecto'); // <- misma clave que usabas en PHP

    $proyectoId = $data['proyecto_id'] ?? null;

    $start = (int)($data['desde_anio'] ?? 2018);
    $end   = (int)($data['hasta_anio'] ?? 2030);
    if ($start > $end) {
        [$start, $end] = [$end, $start];
    }

    // Inicializar arrays de salida (en millones)
    $span = $end - $start + 1;
    $serie = [
        'labels'     => range($start, $end),
        'dinero'     => array_fill(0, $span, 0.0),    // tipo 1
        'especie'    => array_fill(0, $span, 0.0),    // tipo 2
        'industria'  => array_fill(0, $span, 0.0),    // tipo 3
    ];

    // Construye query base (mejor agrupando solo por año y tipo)
    $q = \DB::table('inversion2')
        ->selectRaw('YEAR(Fecha) as Anio, FK_ID_Tipo, SUM(Monto) as TotalMonto')
        // Si tu tabla guarda el nombre del proyecto como "Proyecto" (exacto como en legacy)
        ->when($nombreProyecto, fn($q) => $q->where('Proyecto', $nombreProyecto))
        // Si además tienes columna FK_ID_Proyecto, puedes usarla:
        ->when($proyectoId, fn($q) => $q->where('FK_ID_Proyecto', $proyectoId))
        ->whereBetween(\DB::raw('YEAR(Fecha)'), [$start, $end])
        ->groupBy('Anio', 'FK_ID_Tipo')
        ->orderBy('Anio');

    $rows = $q->get();

    foreach ($rows as $row) {
        $anio = (int)$row->Anio;
        $idx  = $anio - $start;
        if ($idx < 0 || $idx >= $span) continue;

        // En millones (como tu script)
        $montoMillones = ((float)$row->TotalMonto) / 1_000_000;

        switch ((int)$row->FK_ID_Tipo) {
            case 1: // dinero
                $serie['dinero'][$idx] += round($montoMillones, 2);
                break;
            case 2: // especie
                $serie['especie'][$idx] += round($montoMillones, 2);
                break;
            case 3: // industria
                $serie['industria'][$idx] += round($montoMillones, 2);
                break;
        }
    }

    return response()->json($serie);
}

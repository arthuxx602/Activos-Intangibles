<?php

use Illuminate\Support\Facades\Route;

// ===== Controladores =====
use App\Http\Controllers\Admin\{
    AdminController,
    ConsultaController,
    DepartamentoController,
    DescargaController,
    DashboardController,
    EmpresaController,
    InversionController,
    LiquidacionController,
    MunicipioController,
    PaisController,
    ProyectoController,
    ReporteController,
    SimulacionController,
    TasaController,
    TipoInversionController,
    UbicacionController,
    UsuarioController,
    VinculacionController
};

// =====================================================
// RUTA DE PRUEBA
// =====================================================
Route::get('/test', fn () => response()->json(['status' => 'API funcionando ✅']));

// =====================================================
// CRUDs PRINCIPALES (API Resources)
// (cada apiResource ya define index, show, store, update, destroy)
// =====================================================
Route::apiResource('usuarios',        UsuarioController::class);
Route::apiResource('proyectos',       ProyectoController::class);
Route::apiResource('inversiones',     InversionController::class);

Route::apiResource('empresas',        EmpresaController::class);            // si tu app los usa
Route::apiResource('paises',          PaisController::class);
Route::apiResource('departamentos',   DepartamentoController::class);
Route::apiResource('municipios',      MunicipioController::class);
Route::apiResource('tipos-inversion', TipoInversionController::class);
Route::apiResource('tasas',           TasaController::class);
Route::apiResource('ubicaciones',     UbicacionController::class);
// Vinculaciones: NO usamos apiResource (pivot con PK compuesta); abajo van endpoints explícitos.
// Route::apiResource('vinculaciones', VinculacionController::class); // <- NO usar

Route::apiResource('liquidaciones',   LiquidacionController::class);
Route::apiResource('simulaciones',    SimulacionController::class);


// =====================================================
// ACCIONES ESPECÍFICAS EN PROYECTOS (si existen en tu controller)
// =====================================================
Route::post('proyectos/{proyecto}/liquidar', [ProyectoController::class, 'liquidar']);
Route::post('proyectos/{proyecto}/simular',  [ProyectoController::class, 'simular']);

// =====================================================
// DASHBOARD
// =====================================================
Route::get('dashboard/summary', [DashboardController::class, 'summary']);
Route::get('dashboard/proyectos-por-mes', [DashboardController::class, 'proyectosPorMes']);

// =====================================================
// CONSULTAS Y CATÁLOGOS
// =====================================================
Route::get('consultas/usuarios',                        [ConsultaController::class, 'usuarios']);
Route::get('consultas/proyectos-por-usuario/{usuario}', [ConsultaController::class, 'proyectosPorUsuario']);
Route::get('consultas/resumen',                         [ConsultaController::class, 'resumen']);
Route::get('consultas/busqueda',                        [ConsultaController::class, 'busqueda']);

Route::get('catalogos/proyectos-no-liquidados', [ConsultaController::class, 'proyectosNoLiquidados']);
Route::get('simulacion/resumen',                [SimulacionController::class, 'resumen']); // ?proyecto_id=123
Route::get('catalogos/usuarios-para-vincular',  [ConsultaController::class, 'usuariosParaVincular']);
Route::get('tasas/ultima', [TasaController::class, 'ultima']);

// =====================================================
// VINCULACIONES (pivot proyecto_usuario)
// =====================================================
// Listar vínculos con filtros ?proyecto=&usuario=
Route::get('vinculaciones',    [VinculacionController::class, 'index']);
// Sincronizar usuarios de un proyecto (body: { proyecto, usuarios:[] })
Route::post('vinculaciones',   [VinculacionController::class, 'store']);
// Eliminar un vínculo puntual (body: { proyecto, usuario })
Route::delete('vinculaciones', [VinculacionController::class, 'destroy']);

// =====================================================
// REPORTES / GRÁFICOS
// =====================================================
Route::get('reportes/datos-line', [ReporteController::class, 'datosLine']);

// =====================================================
// DESCARGAS
//  recurso: inversiones | proyectos
// =====================================================
Route::get('descargas/{recurso}/{id}/certificado', [DescargaController::class, 'certificado'])
    ->whereIn('recurso', ['inversiones', 'proyectos']);

Route::get('descargas/proyectos-liquidacion/{id}', [DescargaController::class, 'certificado'])
    ->defaults('recurso', 'proyectos-liquidacion');



// (Opcional: listados de descargas si los implementaste)
Route::get('descargas/inversiones', [DescargaController::class, 'inversiones'])->name('descargas.inversiones');
Route::get('descargas/proyectos',   [DescargaController::class, 'proyectos'])->name('descargas.proyectos');

// =====================================================
// OPERACIONES EN LOTE (solo si tus controllers las implementan)
// =====================================================
Route::delete('inversiones',     [InversionController::class,   'destroyMany']);   // DELETE body: { "ids": [...] }
Route::delete('proyectos',       [ProyectoController::class,     'destroyMany']);   // DELETE body: { "ids": [...] }
// Descomenta si creaste los métodos:
// Route::delete('departamentos', [DepartamentoController::class, 'destroyMany']);
// Route::delete('paises',        [PaisController::class,        'destroyMany']);
// Route::delete('municipios',    [MunicipioController::class,   'destroyMany']);

// =====================================================
// COMPATIBILIDAD LEGACY (opcionales)
// =====================================================
Route::post('usuarios/update-legacy',        [UsuarioController::class,       'updateLegacy']);
Route::post('municipios/update-legacy',      [MunicipioController::class,     'updateLegacy']);
Route::post('paises/update-legacy',          [PaisController::class,          'updateLegacy']);
Route::post('tipos-inversion/update-legacy', [TipoInversionController::class, 'updateLegacy']);

// =====================================================
// FALLBACK 404 JSON
// =====================================================
Route::fallback(function () {
    return response()->json([
        'message' => '❌ Ruta no encontrada. Verifica la URL y el método HTTP.'
    ], 404);
});

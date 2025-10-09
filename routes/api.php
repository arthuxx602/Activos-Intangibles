<?php

use Illuminate\Support\Facades\Route;

// ===== Controladores =====
use App\Http\Controllers\Admin\{
    AdminController,
    ConsultaController,
    DepartamentoController,
    DescargaController,
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

// ===== Ruta de prueba =====
Route::get('/test', fn () => response()->json(['status' => 'API funcionando ✅']));



// ===== CRUDs principales (API Resources) =====
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('proyectos', ProyectoController::class);
Route::apiResource('inversiones', InversionController::class);

Route::apiResource('empresas', EmpresaController::class);
Route::apiResource('paises', PaisController::class);
Route::apiResource('departamentos', DepartamentoController::class);
Route::apiResource('municipios', MunicipioController::class);
Route::apiResource('tipos-inversion', TipoInversionController::class);
Route::apiResource('tasas', TasaController::class);
Route::apiResource('ubicaciones', UbicacionController::class);
Route::apiResource('vinculaciones', VinculacionController::class);
Route::apiResource('liquidaciones', LiquidacionController::class);
Route::apiResource('simulaciones', SimulacionController::class);

// ===== Acciones específicas =====
Route::post('proyectos/{proyecto}/liquidar', [ProyectoController::class, 'liquidar']);
Route::post('proyectos/{proyecto}/simular',  [ProyectoController::class, 'simular']);

// ===== Consultas =====
Route::get('consultas/usuarios',                        [ConsultaController::class, 'usuarios']);
Route::get('consultas/proyectos-por-usuario/{usuario}', [ConsultaController::class, 'proyectosPorUsuario']);
Route::get('consultas/resumen',                         [ConsultaController::class, 'resumen']);
Route::get('consultas/busqueda',                        [ConsultaController::class, 'busqueda']);
Route::get('vinculaciones',                             [VinculacionController::class, 'index']);
Route::get('proyectos',                                 [ProyectoController::class, 'index']);
// Catálogos para selects
Route::get('catalogos/proyectos-no-liquidados', [ConsultaController::class, 'proyectosNoLiquidados']);
Route::get('catalogos/usuarios-para-vincular',  [ConsultaController::class, 'usuariosParaVincular']);

// ===== Reportes / Gráficos =====
Route::get('reportes/datos-line', [ReporteController::class, 'datosLine']);

// ===== Descargas =====
// recurso: inversiones | proyectos
Route::get('descargas/{recurso}/{id}/certificado', [DescargaController::class, 'certificado'])
    ->whereIn('recurso', ['inversiones', 'proyectos']);
// Descargas (genérica)
 Route::get('descargas/{recurso}/{id}/certificado', [DescargaController::class, 'certificado'])
    ->whereIn('recurso', ['inversiones', 'proyectos']);

// (Opcional: si implementaste listados de descargas)
Route::get('descargas/inversiones', [DescargaController::class, 'inversiones'])->name('descargas.inversiones');
Route::get('descargas/proyectos',   [DescargaController::class, 'proyectos'])->name('descargas.proyectos');

// ===== Operaciones en lote (si se implemento =====
Route::delete('inversiones', [InversionController::class, 'destroyMany']);   // DELETE body: { "ids": [...] }
Route::delete('proyectos',   [ProyectoController::class, 'destroyMany']);   // DELETE body: { "ids": [...] }
Route::delete('departamentos', [DepartamentoController::class, 'destroyMany']); // opcional (lote)
Route::delete('paises', [PaisController::class, 'destroyMany']);
Route::delete('proyectos', [ProyectoController::class, 'destroyMany']); // opcional para eliminación múltiple
Route::delete('vinculaciones', [VinculacionController::class, 'destroy']);

// Ejemplo opcional:
# Route::delete('municipios', [MunicipioController::class, 'destroyMany']);

// ===== Compatibilidad con formularios legacy (opcionales) =====
Route::post('usuarios/update-legacy',         [UsuarioController::class, 'updateLegacy']);
Route::post('municipios/update-legacy',       [MunicipioController::class, 'updateLegacy']);
Route::post('paises/update-legacy',           [PaisController::class, 'updateLegacy']);
Route::post('tipos-inversion/update-legacy',  [TipoInversionController::class, 'updateLegacy']);
Route::post('vinculaciones',                  [VinculacionController::class, 'store']);

// ===== Fallback genérico (404 JSON) =====
Route::fallback(function () {
    return response()->json([
        'message' => '❌ Ruta no encontrada. Verifica la URL y el método HTTP.'
    ], 404);
});

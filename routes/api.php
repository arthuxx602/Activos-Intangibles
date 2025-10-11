<?php

use Illuminate\Support\Facades\Route;

// ===== Controladores =====
use App\Http\Controllers\Admin\{
    
    AdminController,
    ConsultaController,
    DescargaController,
   // DepartamentoController,
    DashboardController,
    EmpresaController,
    InversionController,
    LiquidacionController,
    MunicipioController,
    PaisController,
    ProyectoController,
    SimulacionController,
    TasaController,
   // TipoInversionController,
    UbicacionController,
    UsuarioController,
    VinculacionController
};


// OJO: TipoInversionController NO está en Admin
use App\Http\Controllers\TipoInversionController;
use App\Http\Controllers\Admin\DepartamentoController;
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
Route::apiResource('empresas',        EmpresaController::class);
Route::apiResource('paises',          PaisController::class);
//Route::apiResource('departamentos',   DepartamentoController::class);
Route::apiResource('municipios',      MunicipioController::class);
Route::apiResource('tipos-inversion', TipoInversionController::class);
Route::apiResource('tasas',           TasaController::class);
Route::apiResource('ubicaciones',     UbicacionController::class);
Route::apiResource('liquidaciones',   LiquidacionController::class);
Route::apiResource('simulaciones',    SimulacionController::class);

// =====================================================
// ACCIONES ESPECÍFICAS EN PROYECTOS / TIPOS
// =====================================================
Route::post('proyectos/{proyecto}/liquidar', [ProyectoController::class, 'liquidar']);
Route::post('proyectos/{proyecto}/simular',  [ProyectoController::class, 'simular']);

// =====================================================
// DASHBOARD
// =====================================================
Route::get('dashboard/summary',          [DashboardController::class, 'summary']);
Route::get('dashboard/proyectos-por-mes',[DashboardController::class, 'proyectosPorMes']);

// =====================================================
// CONSULTAS Y CATÁLOGOS
// =====================================================
Route::get('consultas/usuarios',                        [ConsultaController::class, 'usuarios']);
Route::get('consultas/proyectos-por-usuario/{usuario}', [ConsultaController::class, 'proyectosPorUsuario']);
Route::get('consultas/resumen',                         [ConsultaController::class, 'resumen']);
Route::get('consultas/busqueda',                        [ConsultaController::class, 'busqueda']);

Route::get('catalogos/proyectos-no-liquidados', [ConsultaController::class, 'proyectosNoLiquidados']);
Route::get('catalogos/usuarios-para-vincular',  [ConsultaController::class, 'usuariosParaVincular']);
Route::get('simulacion/resumen',                [SimulacionController::class, 'resumen']); // ?proyecto_id=123
Route::get('tasas/ultima',                      [TasaController::class, 'ultima']);

// =====================================================
// VINCULACIONES (pivot proyecto_usuario)
// =====================================================
Route::get('vinculaciones',    [VinculacionController::class, 'index']);   // ?proyecto=&usuario=
Route::post('vinculaciones',   [VinculacionController::class, 'store']);   // { proyecto, usuarios:[] }
Route::delete('vinculaciones', [VinculacionController::class, 'destroy']); // { proyecto, usuario }

// =====================================================
// DESCARGAS
// =====================================================
Route::get('descargas/{recurso}/{id}/certificado', [DescargaController::class, 'certificado'])
    ->whereIn('recurso', ['inversiones', 'proyectos']);

Route::get('descargas/proyectos-liquidacion/{id}', [DescargaController::class, 'certificado'])
    ->defaults('recurso', 'proyectos-liquidacion');

// Listados opcionales
Route::get('descargas/inversiones', [DescargaController::class, 'inversiones'])->name('descargas.inversiones');
Route::get('descargas/proyectos',   [DescargaController::class, 'proyectos'])->name('descargas.proyectos');

// =====================================================
// OPERACIONES EN LOTE (opcional)
// =====================================================
Route::delete('inversiones', [InversionController::class, 'destroyMany']);
Route::delete('proyectos',   [ProyectoController::class,   'destroyMany']);
// Si implementas más adelante:
// Route::delete('departamentos', [DepartamentoController::class, 'destroyMany']);
// Route::delete('paises',        [PaisController::class,        'destroyMany']);
// Route::delete('municipios',    [MunicipioController::class,   'destroyMany']);

// =====================================================
// COMPATIBILIDAD LEGACY (opcionales)
// =====================================================
Route::post('usuarios/update-legacy',   [UsuarioController::class,   'updateLegacy']);
Route::post('municipios/update-legacy', [MunicipioController::class, 'updateLegacy']);
Route::post('paises/update-legacy',     [PaisController::class,      'updateLegacy']);

// =====================================================
// FALLBACK 404 JSON
// =====================================================
Route::fallback(function () {
    return response()->json([
        'message' => '❌ Ruta no encontrada. Verifica la URL y el método HTTP.'
    ], 404);
});

<?php

use App\Http\Controllers\AgenteController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelitoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\PersonaRegistradaController;
use App\Http\Controllers\RespuestaOficioController;
use App\Http\Controllers\SolicitudOficioController;
use App\Http\Controllers\UnidadController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catálogos
    Route::resource('instituciones', InstitucionController::class);
    Route::resource('unidades', UnidadController::class);
    Route::resource('cargos', CargoController::class);
    Route::resource('agentes', AgenteController::class);
    Route::resource('delitos', DelitoController::class);

    // Personas Registradas
    Route::resource('personas-registradas', PersonaRegistradaController::class);

    // Solicitudes de Oficios
    Route::resource('solicitudes', SolicitudOficioController::class);
    Route::get('api/instituciones/{institucion}/unidades', [SolicitudOficioController::class, 'getUnidadesByInstitucion'])
        ->name('api.instituciones.unidades');
    Route::get('api/unidades/{unidad}/agentes', [SolicitudOficioController::class, 'getAgentesByUnidad'])
        ->name('api.unidades.agentes');
    Route::get('api/instituciones/{institucion}/agentes', [SolicitudOficioController::class, 'getAgentesByInstitucion'])
        ->name('api.instituciones.agentes');

    // Respuestas de Oficios
    Route::resource('respuestas', RespuestaOficioController::class);
    Route::get('solicitudes/{solicitud}/responder', [RespuestaOficioController::class, 'create'])
        ->name('solicitudes.responder');
    Route::get('respuestas/{respuesta}/pdf', [RespuestaOficioController::class, 'generarPdf'])
        ->name('respuestas.pdf');
    Route::post('api/consultar-personas', [RespuestaOficioController::class, 'consultarPersonas'])
        ->name('api.consultar-personas');
});

require __DIR__.'/settings.php';

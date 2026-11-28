<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AlternativaInversionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CuentaController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\EvidenciaDocumentalController;
use App\Http\Controllers\Admin\PartidaController;
use App\Http\Controllers\EvaluacionController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [EvaluacionController::class, 'inicio'])->name('inicio');
Route::get('/dashboard', [EvaluacionController::class, 'dashboard'])->name('dashboard');

// Autenticación administrativa
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Panel Administrativo protegido
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

    // Empresa
    Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
    Route::get('/empresas/{empresa}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
    Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');

    // Alternativas de Inversión
    Route::get('/alternativas/create', [AlternativaInversionController::class, 'create'])->name('alternativas.create');
    Route::post('/alternativas', [AlternativaInversionController::class, 'store'])->name('alternativas.store');
    Route::get('/alternativas/{alternativa}/edit', [AlternativaInversionController::class, 'edit'])->name('alternativas.edit');
    Route::put('/alternativas/{alternativa}', [AlternativaInversionController::class, 'update'])->name('alternativas.update');
    Route::delete('/alternativas/{alternativa}', [AlternativaInversionController::class, 'destroy'])->name('alternativas.destroy');

    // Evidencias Documentales
    Route::post('/alternativas/{alternativa}/evidencias', [EvidenciaDocumentalController::class, 'store'])->name('evidencias.store');
    Route::delete('/evidencias/{evidencia}', [EvidenciaDocumentalController::class, 'destroy'])->name('evidencias.destroy');

    //Cuentas T
    Route::get('/cuentas', [CuentaController::class, 'index'])->name('cuentas.index');
    Route::get('/cuentas/create', [CuentaController::class, 'create'])->name('cuentas.create');
    Route::post('/cuentas', [CuentaController::class, 'store'])->name('cuentas.store');
    Route::delete('/cuentas/{cuenta}', [CuentaController::class, 'destroy'])->name('cuentas.destroy');

    //Registro de partidas
    Route::get('/partidas', [PartidaController::class, 'index'])->name('partidas.index');
    Route::get('/partidas/create', [PartidaController::class, 'create'])->name('partidas.create');
    Route::post('/partidas', [PartidaController::class, 'store'])->name('partidas.store');
    Route::delete('/partidas/{partida}', [PartidaController::class, 'destroy'])->name('partidas.destroy');
});
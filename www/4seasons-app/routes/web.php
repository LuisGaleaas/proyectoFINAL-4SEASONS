<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AlternativaInversionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\Admin\EvidenciaDocumentalController;
use App\Http\Controllers\EvaluacionController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [EvaluacionController::class, 'inicio'])->name('inicio');
Route::get('/dashboard', [EvaluacionController::class, 'dashboard'])->name('dashboard');

// Autenticación administrativa (equivalente a /admin/login/)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Panel Administrativo protegido (equivalente a django.contrib.admin)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

    // Empresa
    Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('empresas.create');
    Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
    Route::get('/empresas/{empresa}/edit', [EmpresaController::class, 'edit'])->name('empresas.edit');
    Route::put('/empresas/{empresa}', [EmpresaController::class, 'update'])->name('empresas.update');

    // Alternativas de Inversión + Inlines de Costos y Fuentes
    Route::get('/alternativas/create', [AlternativaInversionController::class, 'create'])->name('alternativas.create');
    Route::post('/alternativas', [AlternativaInversionController::class, 'store'])->name('alternativas.store');
    Route::get('/alternativas/{alternativa}/edit', [AlternativaInversionController::class, 'edit'])->name('alternativas.edit');
    Route::put('/alternativas/{alternativa}', [AlternativaInversionController::class, 'update'])->name('alternativas.update');
    Route::delete('/alternativas/{alternativa}', [AlternativaInversionController::class, 'destroy'])->name('alternativas.destroy');

    // Evidencias Documentales
    Route::post('/alternativas/{alternativa}/evidencias', [EvidenciaDocumentalController::class, 'store'])->name('evidencias.store');
    Route::delete('/evidencias/{evidencia}', [EvidenciaDocumentalController::class, 'destroy'])->name('evidencias.destroy');
});
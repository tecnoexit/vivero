<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptureController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PlantaController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\PublicPlantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/plantas', [PlantaController::class, 'index'])->name('plantas.index');
    Route::get('/plantas/{planta}', [PlantaController::class, 'show'])->name('plantas.show');
    Route::get('/plantas/{planta}/editar', [PlantaController::class, 'edit'])->middleware('role:admin')->name('plantas.edit');
    Route::put('/plantas/{planta}', [PlantaController::class, 'update'])->middleware('role:admin')->name('plantas.update');
    Route::post('/plantas/{planta}/estado', [PlantaController::class, 'changeState'])->middleware('role:admin,operario')->name('plantas.estado');

    Route::get('/captura/{type}', [CaptureController::class, 'create'])->middleware('role:admin,operario')->name('captura.create');
    Route::post('/captura/{type}', [CaptureController::class, 'store'])->middleware('role:admin,operario')->name('captura.store');

    Route::get('/seleccion', [SelectionController::class, 'index'])->middleware('role:admin')->name('seleccion.index');
    Route::get('/seleccion/exportar', [SelectionController::class, 'export'])->middleware('role:admin')->name('seleccion.export');
    Route::get('/etiquetas', [LabelController::class, 'create'])->middleware('role:admin')->name('etiquetas.create');
    Route::post('/etiquetas', [LabelController::class, 'store'])->middleware('role:admin')->name('etiquetas.store');

    Route::prefix('/admin')->middleware('role:admin')->group(function () {
        Route::get('/{resource}', [CatalogController::class, 'index'])->name('catalogos.index');
        Route::get('/{resource}/crear', [CatalogController::class, 'create'])->name('catalogos.create');
        Route::post('/{resource}', [CatalogController::class, 'store'])->name('catalogos.store');
        Route::get('/{resource}/{id}/editar', [CatalogController::class, 'edit'])->name('catalogos.edit');
        Route::put('/{resource}/{id}', [CatalogController::class, 'update'])->name('catalogos.update');
        Route::delete('/{resource}/{id}', [CatalogController::class, 'destroy'])->name('catalogos.destroy');
    });
});

Route::get('/publico/planta/{token}', [PublicPlantController::class, 'show'])->name('public.planta');

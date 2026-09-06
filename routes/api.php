<?php

use App\Http\Controllers\Api\CatalogApiController;
use App\Http\Controllers\Api\PlantaApiController;
use Illuminate\Support\Facades\Route;

Route::get('/catalogos/{resource}', [CatalogApiController::class, 'index']);
Route::get('/catalogos/{resource}/{id}', [CatalogApiController::class, 'show']);

Route::get('/plantas', [PlantaApiController::class, 'index']);
Route::get('/plantas/{planta}', [PlantaApiController::class, 'show']);

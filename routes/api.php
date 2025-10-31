<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', fn () => 'ok');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
});

Route::middleware('auth:sanctum')->get('/user-data', [UserController::class, 'getUserData']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/documentos', [DocumentoController::class, 'index']);   // listar
    Route::post('/documentos', [DocumentoController::class, 'store']); // registrar
    Route::get('/documentos/{id}', [DocumentoController::class, 'show']); // detalle
});
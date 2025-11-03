<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\TipoDocumentoController;
use App\Http\Controllers\UserController;
use App\Models\Area;
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
    Route::get('/tipo-documentos', [TipoDocumentoController::class, 'index']);
    Route::post('/documentos/{documento}/archivos', [DocumentoController::class, 'uploadArchivos']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/areas', fn() =>
        Area::select('id','nombre')->orderBy('nombre')->get()
    );
});


Route::get('/documentos-publicos', function () {
    return view('public.documentos');
});


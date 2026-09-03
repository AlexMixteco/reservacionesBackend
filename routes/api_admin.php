<?php

use App\Http\Controllers\Admin\AdminBloqueoController;
use App\Http\Controllers\Admin\AdminHorarioController;
use App\Http\Controllers\Admin\AdminNegocioController;
use App\Http\Controllers\Admin\AdminReservacionController;
use App\Http\Controllers\Admin\AdminServicioController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'usuarioActual']);
    Route::get('/reservaciones', [AdminReservacionController::class, 'index']);
    Route::patch('/reservaciones/{id}/nota', [AdminReservacionController::class, 'actualizarNota']);

    Route::get('/servicios', [AdminServicioController::class, 'index']);
    Route::post('/servicios', [AdminServicioController::class, 'store']);
    Route::patch('/servicios/{id}', [AdminServicioController::class, 'update']);
    Route::delete('/servicios/{id}', [AdminServicioController::class, 'destroy']);

    Route::get('/horarios', [AdminHorarioController::class, 'index']);
    Route::put('/horarios/{dia}', [AdminHorarioController::class, 'guardarDia']);

    Route::get('/bloqueos', [AdminBloqueoController::class, 'index']);
    Route::post('/bloqueos', [AdminBloqueoController::class, 'store']);
    Route::delete('/bloqueos/{id}', [AdminBloqueoController::class, 'destroy']);

    Route::get('/negocio', [AdminNegocioController::class, 'show']);
    Route::patch('/negocio', [AdminNegocioController::class, 'update']);
});

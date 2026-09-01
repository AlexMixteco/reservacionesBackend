<?php

use App\Http\Controllers\Admin\AdminReservacionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/admin/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'usuarioActual']);
    Route::get('/reservaciones', [AdminReservacionController::class, 'index']);
    Route::patch('/reservaciones/{id}/nota', [AdminReservacionController::class, 'actualizarNota']);

    // Aquí se irán agregando: servicios, disponibilidad, etc.
});

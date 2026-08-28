<?php

use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\ReservacionController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;

// Flujo público de reserva como invitado — sin autenticación

Route::get('/negocios/{negocio}/servicios', [ServicioController::class, 'index']);
Route::get('/negocios/{negocio}/disponibilidad', [DisponibilidadController::class, 'index']);

Route::post('/negocios/{negocio}/reservaciones', [ReservacionController::class, 'store']);

// Gestión vía enlace único (token), sin login
Route::get('/reservaciones/{token}', [ReservacionController::class, 'show']);
Route::post('/reservaciones/{token}/cancelar', [ReservacionController::class, 'cancelar']);
Route::put('/reservaciones/{token}/reprogramar', [ReservacionController::class, 'reprogramar']);

<?php

use Illuminate\Support\Facades\Route;

// El frontend (Vue) es un proyecto separado — este backend es solo API.
// Esta ruta es únicamente para verificar que el servidor está vivo.
Route::get('/', function () {
    return response()->json(['estado' => 'Zierra API funcionando']);
});

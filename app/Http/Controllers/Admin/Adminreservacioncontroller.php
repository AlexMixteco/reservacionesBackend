<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReservacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // $request->user() es el UsuarioAdmin autenticado — solo ve las
        // reservaciones de SU negocio, nunca las de otro (aislamiento por tenant).
        $negocio = $request->user()->negocio;

        $reservaciones = $negocio->reservaciones()
            ->with(['servicio', 'personal'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        return response()->json($reservaciones);
    }
}

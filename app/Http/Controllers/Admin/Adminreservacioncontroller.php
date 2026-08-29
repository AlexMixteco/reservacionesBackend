<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReservacionController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $negocio = $request->user()->negocio;

        $reservaciones = $negocio->reservaciones()
            ->with(['servicio', 'personal'])
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        return response()->json($reservaciones);
    }
}

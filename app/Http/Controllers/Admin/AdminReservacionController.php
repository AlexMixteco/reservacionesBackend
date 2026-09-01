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
        public function actualizarNota(Request $request, string $id): JsonResponse
    {
        $datos = $request->validate([
            'notas_admin' => 'nullable|string|max:2000',
        ]);

        $reservacion = $request->user()->negocio->reservaciones()->findOrFail($id);
        $reservacion->update($datos);

        return response()->json($reservacion);
    }
}

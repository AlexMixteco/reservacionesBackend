<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use Illuminate\Http\JsonResponse;

class NegocioController extends Controller
{
    public function show(Negocio $negocio): JsonResponse
    {
        return response()->json($negocio->only(['id', 'nombre', 'color_marca']));
    }

    // El frontend del cliente consulta esto para saber si debe mostrar el paso
    // de "¿Con quién?" en el flujo de reserva, o saltárselo.
    public function configuracion(Negocio $negocio): JsonResponse
    {
        return response()->json([
            'requiere_elegir_profesional' => $negocio->requiere_elegir_profesional,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use Illuminate\Http\JsonResponse;

class ServicioController extends Controller
{
    public function index(Negocio $negocio): JsonResponse
    {
        $servicios = $negocio->servicios()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'duracion_minutos', 'precio']);

        return response()->json($servicios);
    }
}

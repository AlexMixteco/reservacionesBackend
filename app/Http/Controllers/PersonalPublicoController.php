<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonalPublicoController extends Controller
{
    public function index(Request $request, Negocio $negocio): JsonResponse
    {
        $request->validate([
            'servicio_id' => 'required|uuid|exists:servicios,id',
        ]);

        $personal = $negocio->personal()
            ->where('activo', true)
            ->whereHas('servicios', fn ($q) => $q->where('servicios.id', $request->servicio_id))
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($personal);
    }
}

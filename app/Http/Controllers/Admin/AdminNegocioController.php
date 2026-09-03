<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNegocioController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user()->negocio);
    }

    public function update(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono_whatsapp' => 'nullable|string|max:30',
            'color_marca' => 'nullable|string|max:7', // ej. "#4F46E5"
        ]);

        $negocio = $request->user()->negocio;
        $negocio->update($datos);

        return response()->json($negocio);
    }
}

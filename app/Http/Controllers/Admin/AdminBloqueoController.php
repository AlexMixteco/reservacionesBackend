<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBloqueoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bloqueos = $request->user()->negocio->bloqueos()
            ->orderBy('fecha')
            ->get();

        return response()->json($bloqueos);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => 'required|date',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'motivo' => 'nullable|string|max:255',
        ]);

        $bloqueo = $request->user()->negocio->bloqueos()->create($datos);

        return response()->json($bloqueo, 201);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()->negocio->bloqueos()->findOrFail($id)->delete();

        return response()->json(['mensaje' => 'Bloqueo eliminado']);
    }
}

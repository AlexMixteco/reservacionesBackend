<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminServicioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $servicios = $request->user()->negocio->servicios()
            ->orderBy('nombre')
            ->get();

        return response()->json($servicios);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'duracion_minutos' => 'required|integer|min:5|max:600',
            'precio' => 'nullable|numeric|min:0',
        ]);

        $servicio = $request->user()->negocio->servicios()->create($datos);

        return response()->json($servicio, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'duracion_minutos' => 'sometimes|required|integer|min:5|max:600',
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'sometimes|boolean',
        ]);

        // Se busca DENTRO de los servicios del negocio del admin autenticado —
        // nunca por un id suelto, para que no pueda editar servicios ajenos.
        $servicio = $request->user()->negocio->servicios()->findOrFail($id);
        $servicio->update($datos);

        return response()->json($servicio);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        // No se borra de verdad: solo se desactiva, para no perder el historial
        // de reservaciones que ya usaron ese servicio.
        $servicio = $request->user()->negocio->servicios()->findOrFail($id);
        $servicio->update(['activo' => false]);

        return response()->json(['mensaje' => 'Servicio desactivado']);
    }
}

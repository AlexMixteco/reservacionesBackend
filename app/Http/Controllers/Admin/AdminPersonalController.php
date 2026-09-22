<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPersonalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $personal = $request->user()->negocio->personal()
            ->with('servicios')
            ->orderBy('nombre')
            ->get();

        return response()->json($personal);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $personal = $request->user()->negocio->personal()->create($datos);

        return response()->json($personal, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'activo' => 'sometimes|boolean',
        ]);

        $persona = $request->user()->negocio->personal()->findOrFail($id);
        $persona->update($datos);

        return response()->json($persona);
    }

    // Reemplaza por completo qué servicios ofrece esta persona (sync: quita los que
    // ya no estén en la lista, agrega los nuevos).
    public function guardarServicios(Request $request, string $id): JsonResponse
    {
        $datos = $request->validate([
            'servicio_ids' => 'array',
            'servicio_ids.*' => 'uuid|exists:servicios,id',
        ]);

        $persona = $request->user()->negocio->personal()->findOrFail($id);
        $persona->servicios()->sync($datos['servicio_ids'] ?? []);

        return response()->json($persona->load('servicios'));
    }

    public function horarios(Request $request, string $id): JsonResponse
    {
        $persona = $request->user()->negocio->personal()->findOrFail($id);

        return response()->json($persona->horariosAtencion()->orderBy('dia_semana')->get());
    }

    // Mismo patrón que AdminHorarioController@guardarDia, pero para el horario
    // PROPIO de este profesional, no el general del negocio.
    public function guardarHorarioDia(Request $request, string $id, int $dia): JsonResponse
    {
        $datos = $request->validate([
            'abierto' => 'required|boolean',
            'hora_inicio' => 'required_if:abierto,true|nullable|date_format:H:i',
            'hora_fin' => 'required_if:abierto,true|nullable|date_format:H:i|after:hora_inicio',
        ]);

        $persona = $request->user()->negocio->personal()->findOrFail($id);

        if (!$datos['abierto']) {
            $persona->horariosAtencion()->where('dia_semana', $dia)->delete();
            return response()->json(['mensaje' => 'Día cerrado para este profesional']);
        }

        $horario = $persona->horariosAtencion()->updateOrCreate(
            ['negocio_id' => $request->user()->negocio_id, 'dia_semana' => $dia],
            ['hora_inicio' => $datos['hora_inicio'], 'hora_fin' => $datos['hora_fin']]
        );

        return response()->json($horario);
    }
}

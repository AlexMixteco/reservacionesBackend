<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminHorarioController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $horarios = $request->user()->negocio->horariosAtencion()
            ->orderBy('dia_semana')
            ->get();

        return response()->json($horarios);
    }

    // Un día sin fila en horarios_atencion = cerrado ese día.
    // Este endpoint abre/cierra un día, o actualiza sus horas si ya está abierto.
    public function guardarDia(Request $request, int $dia): JsonResponse
    {
        $datos = $request->validate([
            'abierto' => 'required|boolean',
            'hora_inicio' => 'required_if:abierto,true|nullable|date_format:H:i',
            'hora_fin' => 'required_if:abierto,true|nullable|date_format:H:i|after:hora_inicio',
        ]);

        $negocio = $request->user()->negocio;

        if (!$datos['abierto']) {

            $negocio->horariosAtencion()->where('dia_semana', $dia)->delete();
            return response()->json(['mensaje' => 'Día cerrado']);
        }

        $horario = $negocio->horariosAtencion()->updateOrCreate(
            ['dia_semana' => $dia],
            ['hora_inicio' => $datos['hora_inicio'], 'hora_fin' => $datos['hora_fin']]
        );

        return response()->json($horario);
    }
}

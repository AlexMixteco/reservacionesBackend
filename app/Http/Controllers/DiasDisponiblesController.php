<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use Illuminate\Http\JsonResponse;

class DiasDisponiblesController extends Controller
{
    public function index(Negocio $negocio): JsonResponse
    {
        // Días de la semana (0=domingo...6=sábado) en los que el negocio SÍ atiende.
        // El calendario del cliente usa esto para deshabilitar días de la semana
        // completos que nunca tienen horario, sin tener que consultar cada fecha.
        $diasAbiertos = $negocio->horariosAtencion()
            ->pluck('dia_semana')
            ->unique()
            ->values();

        // Fechas específicas bloqueadas TODO el día (festivos, vacaciones).
        // Solo se mandan los próximos 90 días para no regresar un historial infinito.
        $fechasBloqueadas = $negocio->bloqueos()
            ->whereNull('hora_inicio') // null = bloqueo de todo el día, no solo una franja
            ->whereBetween('fecha', [now()->toDateString(), now()->addDays(90)->toDateString()])
            ->pluck('fecha')
            ->map(fn ($fecha) => $fecha->format('Y-m-d'))
            ->values();

        return response()->json([
            'dias_abiertos' => $diasAbiertos,
            'fechas_bloqueadas' => $fechasBloqueadas,
        ]);
    }
}

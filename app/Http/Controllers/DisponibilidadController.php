<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    public function index(Request $request, Negocio $negocio): JsonResponse
    {
        $request->validate([
            'servicio_id' => 'required|uuid|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'personal_id' => 'nullable|uuid|exists:personal,id',
        ]);

        $servicio = $negocio->servicios()->findOrFail($request->servicio_id);
        $fecha = Carbon::parse($request->fecha);
        $diaSemana = $fecha->dayOfWeek; // 0 = domingo ... 6 = sábado

        // 1. Horario de atención de ese día
        $horario = $negocio->horariosAtencion()
            ->where('dia_semana', $diaSemana)
            ->first();

        if (!$horario) {
            return response()->json(['horarios_disponibles' => []]);
        }

        // 2. ¿Todo el día está bloqueado? (festivo, vacaciones)
        $bloqueoTotal = $negocio->bloqueos()
            ->whereDate('fecha', $fecha)
            ->whereNull('hora_inicio')
            ->exists();

        if ($bloqueoTotal) {
            return response()->json(['horarios_disponibles' => []]);
        }

        // 3. Generar slots según la duración del servicio
        $duracion = $servicio->duracion_minutos;
        $inicio = Carbon::parse($horario->hora_inicio);
        $fin = Carbon::parse($horario->hora_fin);

        $slots = [];
        $cursor = $inicio->copy();
        while ($cursor->copy()->addMinutes($duracion)->lte($fin)) {
            $slots[] = $cursor->format('H:i');
            $cursor->addMinutes($duracion);
        }

        // 4. Quitar horas ya reservadas (mismo negocio, mismo servicio y, si aplica, mismo personal)
        $reservadas = $negocio->reservaciones()
            ->whereDate('fecha', $fecha)
            ->where('servicio_id', $servicio->id)
            ->when($request->personal_id, fn ($q) => $q->where('personal_id', $request->personal_id))
            ->where('estado', '!=', 'cancelada')
            ->pluck('hora')
            ->map(fn ($hora) => Carbon::parse($hora)->format('H:i'))
            ->toArray();

        // 5. Quitar bloqueos parciales de ese día
        $bloqueosParciales = $negocio->bloqueos()
            ->whereDate('fecha', $fecha)
            ->whereNotNull('hora_inicio')
            ->get();

        $slotsDisponibles = array_values(array_filter($slots, function ($slot) use ($reservadas, $bloqueosParciales) {
            if (in_array($slot, $reservadas)) {
                return false;
            }
            foreach ($bloqueosParciales as $bloqueo) {
                $inicioB = Carbon::parse($bloqueo->hora_inicio)->format('H:i');
                $finB = Carbon::parse($bloqueo->hora_fin)->format('H:i');
                if ($slot >= $inicioB && $slot < $finB) {
                    return false;
                }
            }
            return true;
        }));

        return response()->json(['horarios_disponibles' => $slotsDisponibles]);
    }
}

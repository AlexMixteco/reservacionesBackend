<?php

namespace App\Http\Controllers;

use App\Models\Negocio;
use App\Models\Reservacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservacionController extends Controller
{
    public function store(Request $request, Negocio $negocio): JsonResponse
    {
        $datos = $request->validate([
            'servicio_id' => 'required|uuid|exists:servicios,id',
            'personal_id' => 'nullable|uuid|exists:personal,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
            'numero_personas' => 'nullable|integer|min:1',
            'nombre_cliente' => 'required|string|max:255',
            'telefono_cliente' => 'required|string|max:30',
            'email_cliente' => 'nullable|email',
            'comentario_cliente' => 'nullable|string|max:1000',
        ]);

        // Se usa una transacción con lockForUpdate para que, si dos personas
        // confirman casi al mismo tiempo el mismo horario, la segunda reciba
        // un error claro en vez de crear una reserva duplicada.
        $reservacion = DB::transaction(function () use ($negocio, $datos) {
            $yaOcupado = $negocio->reservaciones()
                ->where('servicio_id', $datos['servicio_id'])
                ->whereDate('fecha', $datos['fecha'])
                ->where('hora', $datos['hora'])
                ->when($datos['personal_id'] ?? null, fn ($q) => $q->where('personal_id', $datos['personal_id']))
                ->where('estado', '!=', 'cancelada')
                ->lockForUpdate()
                ->exists();

            if ($yaOcupado) {
                throw ValidationException::withMessages([
                    'hora' => 'Ese horario ya no está disponible, alguien más lo acaba de reservar.',
                ]);
            }

            return $negocio->reservaciones()->create($datos);
        });

        // TODO: aquí se dispararía el envío por WhatsApp con el enlace de gestión
        // (usa $reservacion->token_gestion para armar la URL, ej. /reservaciones/{token})

        return response()->json($reservacion, 201);
    }

    public function show(string $token): JsonResponse
    {
        $reservacion = Reservacion::where('token_gestion', $token)->firstOrFail();

        return response()->json($reservacion->load(['servicio', 'personal', 'negocio']));
    }

    public function cancelar(string $token): JsonResponse
    {
        $reservacion = Reservacion::where('token_gestion', $token)->firstOrFail();
        $reservacion->update(['estado' => 'cancelada']);

        return response()->json(['mensaje' => 'Reserva cancelada correctamente']);
    }

    public function reprogramar(Request $request, string $token): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
        ]);

        $reservacion = DB::transaction(function () use ($token, $datos) {
            $reservacion = Reservacion::where('token_gestion', $token)->lockForUpdate()->firstOrFail();

            $yaOcupado = Reservacion::where('negocio_id', $reservacion->negocio_id)
                ->where('servicio_id', $reservacion->servicio_id)
                ->where('id', '!=', $reservacion->id)
                ->whereDate('fecha', $datos['fecha'])
                ->where('hora', $datos['hora'])
                ->when($reservacion->personal_id, fn ($q) => $q->where('personal_id', $reservacion->personal_id))
                ->where('estado', '!=', 'cancelada')
                ->lockForUpdate()
                ->exists();

            if ($yaOcupado) {
                throw ValidationException::withMessages([
                    'hora' => 'Ese horario ya no está disponible, elige otro.',
                ]);
            }

            $reservacion->update($datos);

            return $reservacion;
        });

        return response()->json($reservacion);
    }
}

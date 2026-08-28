<?php

namespace App\Http\Controllers;

use App\Models\UsuarioAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $usuario = UsuarioAdmin::where('email', $datos['email'])->first();

        if (!$usuario || !Hash::check($datos['password'], $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => 'Correo o contraseña incorrectos.',
            ]);
        }

        $token = $usuario->createToken('panel-admin')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => $usuario->only(['id', 'nombre', 'email', 'rol', 'negocio_id']),

        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    public function usuarioActual(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}

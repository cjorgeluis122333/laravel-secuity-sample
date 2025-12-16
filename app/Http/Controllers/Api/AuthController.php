<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            // Crear el usuario
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Obtener el nombre del dispositivo desde la solicitud (o un valor por defecto)
            $deviceName = $request->input('device_name', 'web');

            // Crear un token de acceso con el nombre del dispositivo
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Iniciar sesion (login)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            // Buscar el usuario
            $user = User::where('email', $validated['email'])->first();

            // Verificar que el usuario existe y la contrasena es correcta
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales invalidas',
                ], 401);
            }

            // Revocar todos los tokens exist// Obtener el nombre del dispositivo desde la solicitud (o un valor por defecto)
            $deviceName = $request->header('User-Agent') ?? $request->input('device_name', 'web');

            // Revocar tokens existentes para el mismo dispositivo
            $user->tokens()->where('name', $deviceName)->delete();

            // Crear un nuevo token de acceso con el nombre del dispositivo
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Sesion iniciada exitosamente',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validacion',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar sesion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cerrar sesion (logout)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesion cerrada exitosamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesion',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refrescar el token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();

            // Revocar el token anterior
            $currentAccessToken = $user->currentAccessToken();
            $deviceName = $currentAccessToken->name ?? 'web';
            $currentAccessToken->delete();

            // Crear un nuevo token con el mismo nombre de dispositivo
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => [
                    'user' => $user,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar el token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener el usuario autenticado
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $request->user(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

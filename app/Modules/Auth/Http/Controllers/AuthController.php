<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Application\UseCases\GetAuthenticatedUser;
use App\Modules\Auth\Application\UseCases\LoginUser;
use App\Modules\Auth\Application\UseCases\LogoutUser;
use App\Modules\Auth\Application\UseCases\RefreshToken;
use App\Modules\Auth\Application\UseCases\RegisterUser;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @group Autenticación
 *
 * Endpoints para registro, login, obtención de usuario autenticado y gestión de tokens.
 */
class AuthController extends Controller
{
    protected RegisterUser $registerUser;

    protected LoginUser $loginUser;

    protected LogoutUser $logoutUser;

    protected RefreshToken $refreshToken;

    protected GetAuthenticatedUser $getAuthenticatedUser;

    public function __construct(
        RegisterUser $registerUser,
        LoginUser $loginUser,
        LogoutUser $logoutUser,
        RefreshToken $refreshToken,
        GetAuthenticatedUser $getAuthenticatedUser
    ) {
        $this->registerUser = $registerUser;
        $this->loginUser = $loginUser;
        $this->logoutUser = $logoutUser;
        $this->refreshToken = $refreshToken;
        $this->getAuthenticatedUser = $getAuthenticatedUser;
    }

    /**
     * Iniciar sesión
     *
     * Inicia sesión con email y contraseña, devuelve un token JWT.
     *
     *  @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();
            $token = $this->loginUser->handle($data['email'], $data['password']);
            if (! $token) {
                Log::error('"AuthController": Error during login', ['email' => $data['email']]);

                return response()->json(['error' => 'Credenciales Invalidas o Usuario Inactivo'], 401);
            } else {
                Log::info('"AuthController": User logged in', ['email' => $data['email']]);

                return response()->json([
                    'success' => true,
                    'message' => 'Ingreso exitoso',
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ], 200);
            }
        } catch (Exception $e) {
            Log::error('"AuthController": Error during login', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Credenciales Invalidas o Usuario Inactivo',
            ], 401);
        }
    }

    /**
     * Registrar usuario
     *
     * Registra un nuevo usuario en el sistema.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $this->registerUser->handle($data);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado !',
            ], 201);
        } catch (\Exception $e) {
            Log::error('"AuthController": Error during register', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Falla en el registro: '.$e->getMessage(),
            ], 400);
        }
    }

    /**
     * Obtener usuario autenticado
     *
     * Devuelve la información del usuario autenticado mediante el token JWT.
     */
    public function me()
    {
        try {
            $user = $this->getAuthenticatedUser->handle();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('"AuthController": Error during get me', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user: '.$e->getMessage(),
            ], 400);
        }
    }

    /**
     * Refrescar token
     *
     * Devuelve un nuevo token JWT a partir de uno válido.
     */
    public function refresh()
    {
        try {
            $newToken = $this->refreshToken->handle();

            return response()->json([
                'success' => true,
                'message' => 'Token actualizada con éxito',
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (Exception $e) {
            Log::error('"AuthController": Error during token refresh', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed',
            ], 401);
        }
    }

    /**
     * Cerrar sesión
     *
     * Invalida el token actual y cierra la sesión del usuario.
     */
    public function logout()
    {
        try {
            $this->logoutUser->handle();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada con éxito',
            ]);
        } catch (Exception $e) {
            Log::error('"AuthController": Error during logout', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo cerrar la sesión',
            ], 500);
        }
    }
}

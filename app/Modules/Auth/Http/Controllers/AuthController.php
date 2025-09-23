<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\Auth\Application\UseCases\LoginUser;
use App\Modules\Auth\Application\UseCases\RegisterUser;
use App\Modules\Auth\Application\UseCases\LogoutUser;
use App\Modules\Auth\Application\UseCases\RefreshToken;
use App\Modules\Auth\Application\UseCases\GetAuthenticatedUser;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Este código implementa varios principios SOLID:
 *
 * 1. Single Responsibility Principle (SRP): Cada clase o función tiene una única responsabilidad,
 *    facilitando su mantenimiento y comprensión.
 * 2. Open/Closed Principle (OCP): El código está diseñado para ser abierto a extensión pero cerrado a modificación,
 *    permitiendo agregar nuevas funcionalidades sin alterar el código existente.
 * 3. Liskov Substitution Principle (LSP): Las clases derivadas pueden sustituir a sus clases base sin alterar el funcionamiento del programa.
 * 4. Interface Segregation Principle (ISP): Las interfaces están divididas según sus responsabilidades, evitando que los clientes dependan de métodos que no utilizan.
 * 5. Dependency Inversion Principle (DIP): El código depende de abstracciones y no de implementaciones concretas, facilitando la flexibilidad y el desacoplamiento.
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
     * Handle user login.
     * @unauthenticated
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $token = $this->loginUser->handle($data['email'], $data['password']);
        if (!$token) {
            return response()->json(['error' => 'Invalid credentials or inactive user'], 401);
        }
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ], 200);
    }

    /**
     * Handle user registration.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $this->registerUser->handle($data);
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle user autenticado.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function me()
    {
        try {
            $user = $this->getAuthenticatedUser->handle();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado.'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user: ' . $e->getMessage()
            ], 400);
        }
    }


    /**
     * Refresh a token.
     */
    public function refresh()
    {
        try {
            $newToken = $this->refreshToken->handle();
            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);
        } catch (Exception $e) {
            Log::error('"AuthController": Error during token refresh', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed'
            ], 401);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        try {
            $this->logoutUser->handle();


            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (Exception $e) {
            Log::error('"AuthController": Error during logout', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }
}

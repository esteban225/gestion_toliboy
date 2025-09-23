<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role_id' => 'nullable|integer|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id ?? 1, // Default role
            ]);

            $token = JWTAuth::fromUser($user);

            Log::info('User registered successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user->load('role'),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = JWTAuth::parseToken()->authenticate();

            Log::info('User logged in successfully', ['user_id' => $user->id, 'email' => $user->email]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user->load('role'),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);

        } catch (JWTException $e) {
            Log::error('JWT Error during login', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Could not create token'
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user->load('role')
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user info', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving user information'
            ], 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        try {
            $user = \Illuminate\Support\Facades\Auth::user();

            JWTAuth::invalidate(JWTAuth::getToken());

            Log::info('User logged out successfully', ['user_id' => $user->id ?? null]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);

        } catch (JWTException $e) {
            Log::error('JWT Error during logout', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]);

        } catch (JWTException $e) {
            Log::error('JWT Error during token refresh', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed'
            ], 401);
        }
    }

    /**
     * Get user permissions based on role
     */
    public function permissions()
    {
        try {
            $authUser = \Illuminate\Support\Facades\Auth::user();

            if (!$authUser || !$authUser->role_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User or role not found'
                ], 404);
            }

            // Retrieve the user as an Eloquent model to eager load 'role'
            $user = \App\Models\User::with('role')->find($authUser->id);

            // Define permissions based on role
            $permissions = $this->getUserPermissions($user->role->name);

            return response()->json([
                'success' => true,
                'user' => $user,
                'permissions' => $permissions
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving user permissions', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving permissions'
            ], 500);
        }
    }

    /**
     * Get permissions array based on role name
     */
    private function getUserPermissions(string $roleName): array
    {
        $permissions = [
            'DEV' => [
                'users' => ['create', 'read', 'update', 'delete'],
                'roles' => ['create', 'read', 'update', 'delete'],
                'forms' => ['create', 'read', 'update', 'delete'],
                'batches' => ['create', 'read', 'update', 'delete'],
                'products' => ['create', 'read', 'update', 'delete'],
                'inventory' => ['create', 'read', 'update', 'delete'],
                'reports' => ['create', 'read', 'export'],
                'dashboard' => ['view', 'manage'],
            ],
            'GG' => [
                'forms' => ['read'],
                'batches' => ['read'],
                'products' => ['read'],
                'inventory' => ['read'],
                'reports' => ['read', 'export'],
                'dashboard' => ['view'],
                'notifications' => ['read'],
            ],
            'INPL' => [
                'forms' => ['create', 'read', 'update'],
                'batches' => ['read'],
                'work_logs' => ['create', 'read', 'update'],
                'reports' => ['read'],
                'dashboard' => ['view'],
            ],
            'INPR' => [
                'forms' => ['create', 'read', 'update'],
                'batches' => ['read'],
                'work_logs' => ['create', 'read', 'update'],
                'reports' => ['read'],
                'dashboard' => ['view'],
            ],
            'TRZ' => [
                'forms' => ['read'],
                'batches' => ['read'],
                'reports' => ['read'],
            ],
            'OP' => [
                'forms' => ['create', 'read'],
                'work_logs' => ['create', 'read'],
                'reports' => ['read'],
            ],
        ];

        return $permissions[$roleName] ?? [];
    }
}

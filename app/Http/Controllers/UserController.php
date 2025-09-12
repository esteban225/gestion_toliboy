<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de usuarios.
 * Proporciona métodos para listar, crear, mostrar, editar, actualizar y eliminar usuarios.
 */
class UserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con todos los usuarios.
     */
    public function index()
    {
        // Obtiene todos los usuarios de la base de datos
        $users = User::all();
        // Retorna los usuarios en formato JSON
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No se encuentran usuarios'], 404);
        } else {
            return response()->json([
                'status' => true,
                'message' => 'Usuarios encontrados',
                'data' => $users
            ], 200);
        }
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * (No implementado, ya que normalmente se usa en aplicaciones web con vistas)
     *
     * @return void
     */
    public function create() {}

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|nullable|integer|exists:roles,id',
            'position' => 'required|nullable|string|max:255',
        ]);

        // Si la validación falla, retorna los errores
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Encripta la contraseña antes de guardar
        $request->merge(['password' => bcrypt($request->password)]);

        // Intenta crear el usuario y maneja posibles errores
        try {
            $user = User::create($request->all());

            if ($user) {
                // Si el usuario se creó correctamente
                return response()->json([
                    'status' => true,
                    'message' => 'Usuario creado exitosamente',
                ], 201);
            } else {
                // Si no se pudo crear el usuario
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo crear el registro',
                ], 500);
            }
        } catch (\Exception $e) {
            // Si ocurre una excepción al crear el usuario
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de un usuario específico.
     *
     * @param string $id Identificador del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos del usuario o mensaje de error.
     */
    public function show(string $id)
    {
        try {
            // Busca el usuario por su ID
            $user = User::findOrFail($id);
            if ($user) {
                // Si el usuario existe, retorna sus datos
                return response()->json([
                    'status' => true,
                    'data' => $user
                ], 200);
            } else {
                // Si el usuario no existe
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el usuario
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Si ocurre una excepción
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para editar un usuario específico.
     * (No implementado)
     *
     * @param string $id Identificador del usuario.
     * @return void
     */
    public function edit(string $id) {}

    /**
     * Actualiza la información de un usuario específico en la base de datos.
     *
     * @param \Illuminate\Http\Request $request Datos de la solicitud HTTP.
     * @param string $id Identificador del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Busca el usuario por su ID
            $user = User::findOrFail($id);
            if (!$user) {
                // Si el usuario no existe
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            // Validación de los datos recibidos
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
                'password' => 'sometimes|required|string|min:8',
                'role_id' => 'sometimes|required|nullable|integer|exists:roles,id',
                'position' => 'sometimes|required|nullable|string|max:255',
            ]);

            // Si la validación falla, retorna los errores
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Encripta la contraseña si se proporciona
            if ($request->has('password')) {
                $request->merge(['password' => bcrypt($request->password)]);
            }

            // Actualiza el usuario con los datos validados
            $user->update($request->all());

            // Retorna respuesta exitosa con los datos actualizados
            return response()->json([
                'status' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el usuario
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Si ocurre una excepción
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un usuario específico de la base de datos.
     *
     * @param string $id Identificador del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la operación.
     */
    public function destroy(string $id)
    {
        try {
            // Busca el usuario por su ID
            $user = User::findOrFail($id);
            if (!$user) {
                // Si el usuario no existe
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            // Elimina el usuario
            $user->delete();

            // Retorna respuesta exitosa
            return response()->json([
                'status' => true,
                'message' => 'Usuario eliminado exitosamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Si no se encuentra el usuario
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Si ocurre una excepción
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

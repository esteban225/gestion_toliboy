<?php

namespace App\Http\Controllers;

use App\Models\PersonalDatum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataUser = PersonalDatum::all();
        if ($dataUser->isEmpty()) {
            return response()->json(['message' => 'No se encuentran datos de usuarios', 404]);
        }

        if ($dataUser) {
            $data = [
                'status' => true,
                'message' => 'Usuarios encontrados',
                'data' => $dataUser
            ];
            return response()->json($data, 201);
        };
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Corrected validation rules
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id|unique:personal_data,user_id',
            'num_phone' => 'nullable|string|max:20',
            'num_phone_alt' => 'nullable|string|max:20',
            // 'personal_data' is likely a typo, it should be 'personal_data'
            'num_identification' => 'nullable|string|max:50|unique:personal_data,num_identification',
            'identification_type' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:45',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:25',
        ]);

        // If validation fails, return a 422 response with the errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // Use a try-catch block for robust error handling during creation
        try {
            $dataUser = PersonalDatum::create($request->all());

            if ($dataUser) {
                return response()->json([
                    'status' => true,
                    'message' => 'Datos de usuario creados exitosamente',
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No se pudo crear el registro',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dataUser = PersonalDatum::where('user_id', $id)->first();
        if (!$dataUser) {
            return response()->json(['message' => 'No se encuentran datos del usuario'], 404);
        }
        if ($dataUser) {
            $data = [
                'status' => true,
                'message' => 'Datos de usuario encontrados',
                'data' => $dataUser
            ];
            return response()->json($data, 201);
        };
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dataUser = PersonalDatum::where('user_id', $id)->first();
        if (!$dataUser) {
            return response()->json(['message' => 'No se encuentran datos del usuario'], 404);
        }

        $validator = Validator::make($request->all(), [
            'num_phone' => 'nullable|string|max:20',
            'num_phone_alt' => 'nullable|string|max:20',
            'num_identification' => 'nullable|string|max:50|unique:personal_data,num_identification,' . $dataUser->id,
            'identification_type' => 'nullable|string|max:45',
            'address' => 'nullable|string|max:45',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:25',
        ]);

        // If validation fails, return a 422 response with the errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $dataUser->update($request->all());
        if ($dataUser) {
            $data = [
                'status' => true,
                'message' => 'Datos de usuario actualizados exitosamente',
                'data' => $dataUser
            ];
        }
        return response()->json($data, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dataUser = PersonalDatum::where('user_id', $id)->first();
        if (!$dataUser) {
            return response()->json(['message' => 'No se encuentran datos del usuario'], 404);
        }
        $dataUser->delete();
        if ($dataUser) {
            $data = [
                'status' => true,
                'message' => 'Datos de usuario eliminados exitosamente',
            ];
        }
        return response()->json($data, 201);
    }
}

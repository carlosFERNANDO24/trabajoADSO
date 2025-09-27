<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return response()->json($pacientes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documento' => 'required|string|max:20|unique:pacientes',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $paciente = Paciente::create($validator->validated());
        return response()->json($paciente, 201);
    }

    public function citasPorPaciente(string $id)
{
    $paciente = Paciente::with('citas.medico')->find($id);
    
    if (!$paciente) {
        return response()->json(['message' => 'Paciente no encontrado'], 404);
    }
    
    return response()->json($paciente->citas);
}

    public function show(string $id)
    {
        $paciente = Paciente::find($id);
        
        if (!$paciente) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }
        
        return response()->json($paciente);
    }

    public function update(Request $request, string $id)
    {
        $paciente = Paciente::find($id);
        
        if (!$paciente) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'documento' => 'string|max:20|unique:pacientes,documento,'.$id,
            'nombre' => 'string|max:255',
            'apellido' => 'string|max:255',
            'fecha_nacimiento' => 'date',
            'genero' => 'in:F,M',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $paciente->update($validator->validated());
        return response()->json($paciente);
    }

    public function destroy(string $id)
    {
        $paciente = Paciente::find($id);
        
        if (!$paciente) {
            return response()->json(['message' => 'Paciente no encontrado'], 404);
        }

        $paciente->delete();
        return response()->json(['message' => 'Paciente eliminado correctamente']);
    }

    public function pacientesMayores60()
{
    $hace60Anios = now()->subYears(60)->format('Y-m-d');
    $pacientes = Paciente::where('fecha_nacimiento', '<=', $hace60Anios)->get();
    
    return response()->json($pacientes);
}
    
    public function storeMiPaciente(Request $request)
    {
        $user = $request->user();

        // ✅ CORRECCIÓN: Usar el email para validar que no exista ya un perfil de paciente
        $pacienteExistente = Paciente::where('email', $user->email)->first();
        if ($pacienteExistente) {
            return response()->json(['message' => 'El paciente ya tiene un perfil creado.'], 409);
        }
        
        $validator = Validator::make($request->all(), [
            'documento' => 'required|string|max:20|unique:pacientes',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:M,F',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $paciente = Paciente::create(array_merge(
            $validator->validated(),
            // ✅ CORRECCIÓN: Usar el email del usuario para vincular el paciente
            ['nombre' => $user->name, 'email' => $user->email]
        ));

        return response()->json(['message' => 'Perfil de paciente creado exitosamente', 'paciente' => $paciente], 201);
    }

    public function getMyProfile(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('email', $user->email)->first();

        if (!$paciente) {
            return response()->json(['message' => 'Perfil de paciente no encontrado.'], 404);
        }

        return response()->json($paciente);
    }
}
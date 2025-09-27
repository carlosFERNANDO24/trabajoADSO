<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente; // ✅ Importa el modelo Paciente
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::with(['paciente', 'medico'])->get();
        return response()->json($citas);
    }


    public function historialPorCita(string $id)
    {
        $cita = Cita::with('historialMedico')->find($id);
        
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        
        return response()->json($cita->historialMedico);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:programada,completada,cancelada',
            'motivo_consulta' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita = Cita::create($validator->validated());
        return response()->json($cita->load(['paciente', 'medico']), 201);
    }

    public function show(string $id)
    {
        $cita = Cita::with(['paciente', 'medico'])->find($id);
        
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        
        return response()->json($cita);
    }

    public function update(Request $request, string $id)
    {
        $cita = Cita::find($id);
        
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'date',
            'estado' => 'in:programada,completada,cancelada',
            'motivo_consulta' => 'string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'paciente_id' => 'exists:pacientes,id',
            'medico_id' => 'exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita->update($validator->validated());
        return response()->json($cita->load(['paciente', 'medico']));
    }

    public function destroy(string $id)
    {
        $cita = Cita::find($id);
        
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $cita->delete();
        return response()->json(['message' => 'Cita eliminada correctamente']);
    }

    /**
     * 🔹 Mostrar solo las citas del paciente autenticado
     */
    public function misCitas(Request $request)
    {
        $user = $request->user();

        // ✅ CORRECCIÓN: Buscar el paciente por email para obtener su ID
        $paciente = Paciente::where('email', $user->email)->first();

        if (!$paciente) {
            return response()->json(['message' => 'Acceso denegado. Perfil de paciente no encontrado.'], 403);
        }

        $citas = Cita::with(['paciente', 'medico'])
                    ->where('paciente_id', $paciente->id)
                    ->get();

        return response()->json($citas);
    }
    
    public function crearMiCita(Request $request)
    {
        $user = $request->user();

        $paciente = Paciente::where('email', $user->email)->first();

        // Aseguramos que el usuario tiene un perfil de paciente
        if (!$paciente) {
            return response()->json(['error' => 'El usuario no tiene un registro de paciente asociado.'], 403);
        }
        
        $request->merge(['paciente_id' => $paciente->id]);

        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'required|date',
            'estado' => 'required|in:programada,completada,cancelada',
            'motivo_consulta' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'medico_id' => 'required|exists:medicos,id',
            'paciente_id' => 'required|exists:pacientes,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $cita = Cita::create($validator->validated());
        return response()->json($cita->load(['paciente', 'medico']), 201);
    }
}
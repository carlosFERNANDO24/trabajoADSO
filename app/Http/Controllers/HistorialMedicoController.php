<?php

namespace App\Http\Controllers;

use App\Models\HistorialMedico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HistorialMedicoController extends Controller
{
    public function index()
    {
        $historiales = HistorialMedico::with(['paciente', 'medico', 'cita'])->get();
        return response()->json($historiales);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diagnostico' => 'required|string|max:2000',
            'tratamiento' => 'required|string|max:2000',
            'notas' => 'nullable|string|max:2000',
            'fecha_consulta' => 'required|date',
            'cita_id' => 'required|exists:citas,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $historial = HistorialMedico::create($validator->validated());
        return response()->json($historial->load(['paciente', 'medico', 'cita']), 201);
    }

    public function show(string $id)
    {
        $historial = HistorialMedico::with(['paciente', 'medico', 'cita'])->find($id);
        
        if (!$historial) {
            return response()->json(['message' => 'Historial médico no encontrado'], 404);
        }
        
        return response()->json($historial);
    }

    public function update(Request $request, string $id)
    {
        $historial = HistorialMedico::find($id);
        
        if (!$historial) {
            return response()->json(['message' => 'Historial médico no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'diagnostico' => 'string|max:2000',
            'tratamiento' => 'string|max:2000',
            'notas' => 'nullable|string|max:2000',
            'fecha_consulta' => 'date',
            'cita_id' => 'exists:citas,id',
            'paciente_id' => 'exists:pacientes,id',
            'medico_id' => 'exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $historial->update($validator->validated());
        return response()->json($historial->load(['paciente', 'medico', 'cita']));
    }

    public function destroy(string $id)
    {
        $historial = HistorialMedico::find($id);
        
        if (!$historial) {
            return response()->json(['message' => 'Historial médico no encontrado'], 404);
        }

        $historial->delete();
        return response()->json(['message' => 'Historial médico eliminado correctamente']);
    }

    public function historialPorPaciente(string $pacienteId)
    {
        $historial = HistorialMedico::with(['paciente', 'medico', 'cita'])
                    ->where('paciente_id', $pacienteId)
                    ->get();
        
        if ($historial->isEmpty()) {
            return response()->json(['message' => 'No se encontró historial médico para este paciente'], 404);
        }
        
        return response()->json($historial);
    }

    /**
     * ✅ FUNCIÓN AGREGADA: Mostrar solo el historial del paciente autenticado
     */
    public function miHistorial(Request $request)
    {
        $user = $request->user();

        // Aseguramos que sea un paciente
        if ($user->role !== 'paciente') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $historiales = HistorialMedico::with(['paciente', 'medico', 'cita'])
                        ->where('paciente_id', $user->id)
                        ->get();

        return response()->json($historiales);
    }
}

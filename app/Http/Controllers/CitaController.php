<?php

namespace App\Http\Controllers;

use App\Models\Cita;
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

        // Aseguramos que sea un paciente
        if ($user->role !== 'paciente') {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $citas = Cita::with(['paciente', 'medico'])
                    ->where('paciente_id', $user->id)
                    ->get();

        return response()->json($citas);
    }
}

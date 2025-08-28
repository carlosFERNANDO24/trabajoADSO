<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::all();
        return response()->json($medicos);
    }
    public function citasPorMedico(string $id)
{
    $medico = Medico::with('citas.paciente')->find($id);
    
    if (!$medico) {
        return response()->json(['message' => 'Médico no encontrado'], 404);
    }
    
    return response()->json($medico->citas);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'documento' => 'required|string|max:20|unique:medicos',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'especialidad' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $medico = Medico::create($validator->validated());
        return response()->json($medico, 201);
    }

    public function show(string $id)
    {
        $medico = Medico::find($id);
        
        if (!$medico) {
            return response()->json(['message' => 'Médico no encontrado'], 404);
        }
        
        return response()->json($medico);
    }

    public function update(Request $request, string $id)
    {
        $medico = Medico::find($id);
        
        if (!$medico) {
            return response()->json(['message' => 'Médico no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'documento' => 'string|max:20|unique:medicos,documento,'.$id,
            'nombre' => 'string|max:255',
            'apellido' => 'string|max:255',
            'especialidad' => 'string|max:255',
            'telefono' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $medico->update($validator->validated());
        return response()->json($medico);
    }

    public function destroy(string $id)
    {
        $medico = Medico::find($id);
        
        if (!$medico) {
            return response()->json(['message' => 'Médico no encontrado'], 404);
        }

        $medico->delete();
        return response()->json(['message' => 'Médico eliminado correctamente']);
    }
    public function medicosPorEspecialidad(Request $request, string $especialidad)
{
    $medicos = Medico::where('especialidad', $especialidad)->get();
    
    if ($medicos->isEmpty()) {
        return response()->json(['message' => 'No se encontraron médicos con esa especialidad'], 404);
    }
    
    return response()->json($medicos);
}
}
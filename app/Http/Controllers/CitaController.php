<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; // Importa la clase Mail
use App\Mail\CitaCreadaMail;       // Importa tu nuevo Mailable
use Illuminate\Support\Facades\Log; // Opcional, pero recomendado para depurar

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carga las relaciones para evitar N+1 queries y ordena por fecha más reciente
        $citas = Cita::with(['paciente', 'medico'])->orderBy('fecha_hora', 'desc')->get();
        return response()->json($citas);
    }

    /**
     * Store a newly created resource in storage.
     * (Este método es para Admin/Doctor)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'required|date|after_or_equal:now',
            'estado' => 'required|in:programada,confirmada,completada,cancelada',
            'motivo_consulta' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $cita = Cita::create($validator->validated());
            // Carga las relaciones para usarlas en la respuesta y el correo
            $citaConRelaciones = $cita->load(['paciente', 'medico']);

            // --- INICIO: LÓGICA PARA ENVIAR CORREO DE CONFIRMACIÓN ---
            $paciente = $citaConRelaciones->paciente;

            // Verifica si el paciente existe y tiene un correo electrónico
            if ($paciente && $paciente->email) {
                try {
                    Mail::to($paciente->email)->send(new CitaCreadaMail($citaConRelaciones, $paciente));
                    Log::info("Correo de confirmación enviado a {$paciente->email} para la cita ID: {$cita->id}");
                } catch (\Exception $e) {
                    // Si el envío de correo falla, lo registra en el log pero no detiene la operación.
                    Log::error("FALLO al enviar correo para la cita ID {$cita->id}: " . $e->getMessage());
                }
            } else {
                 Log::warning("No se pudo enviar correo para la cita ID {$cita->id}: Paciente no tiene un email registrado.");
            }
            // --- FIN: LÓGICA PARA ENVIAR CORREO ---

            return response()->json($citaConRelaciones, 201);

        } catch (\Exception $e) {
            Log::error("Error al crear la cita en la base de datos: " . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor al crear la cita.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cita = Cita::with(['paciente', 'medico', 'historialMedico'])->find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        return response()->json($cita);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'sometimes|required|date|after_or_equal:now',
            'estado' => 'sometimes|required|in:programada,confirmada,completada,cancelada',
            'motivo_consulta' => 'sometimes|required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'paciente_id' => 'sometimes|required|exists:pacientes,id',
            'medico_id' => 'sometimes|required|exists:medicos,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cita->update($validator->validated());
        return response()->json($cita->load(['paciente', 'medico']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cita = Cita::find($id);
        if (!$cita) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        $cita->delete();
        return response()->json(['message' => 'Cita eliminada exitosamente']);
    }

    // --- MÉTODOS ESPECÍFICOS PARA PACIENTES ---

    public function misCitas(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('email', $user->email)->first();

        if (!$paciente) {
            return response()->json(['message' => 'No se encontró un paciente asociado a este usuario.'], 404);
        }

        $citas = Cita::where('paciente_id', $paciente->id)
                     ->with(['medico'])
                     ->orderBy('fecha_hora', 'asc')
                     ->get();

        return response()->json($citas);
    }

    public function crearMiCita(Request $request)
    {
        $user = $request->user();
        $paciente = Paciente::where('email', $user->email)->first();

        if (!$paciente) {
            return response()->json(['message' => 'El usuario no tiene un registro de paciente asociado.'], 403);
        }

        $request->merge(['paciente_id' => $paciente->id]);

        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'required|date|after_or_equal:now',
            'motivo_consulta' => 'required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'medico_id' => 'required|exists:medicos,id',
            'paciente_id' => 'required|exists:pacientes,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $datosValidados = $validator->validated();
            $datosValidados['estado'] = 'programada'; // El paciente siempre crea citas como 'programada'

            $cita = Cita::create($datosValidados);
            $citaConRelaciones = $cita->load(['paciente', 'medico']);

            // --- INICIO: ENVIAR CORREO (PACIENTE CREA SU CITA) ---
            if ($paciente && $paciente->email) {
                try {
                    Mail::to($paciente->email)->send(new CitaCreadaMail($citaConRelaciones, $paciente));
                    Log::info("Correo de confirmación enviado a {$paciente->email} para la cita ID: {$cita->id} (creada por paciente).");
                } catch (\Exception $e) {
                    Log::error("FALLO al enviar correo para la cita ID {$cita->id} (creada por paciente): " . $e->getMessage());
                }
            }
            // --- FIN: ENVIAR CORREO ---

            return response()->json($citaConRelaciones, 201);

        } catch (\Exception $e) {
            Log::error("Error al crear la cita para el paciente {$paciente->id}: " . $e->getMessage());
            return response()->json(['message' => 'Error interno del servidor al crear la cita.'], 500);
        }
    }

    public function actualizarMiCita(Request $request, string $id)
    {
        $user = $request->user();
        $paciente = Paciente::where('email', $user->email)->first();
        if (!$paciente) { return response()->json(['message' => 'No se encontró un paciente asociado.'], 403); }

        $cita = Cita::where('id', $id)->where('paciente_id', $paciente->id)->first();
        if (!$cita) { return response()->json(['message' => 'Cita no encontrada o no pertenece a este paciente.'], 404); }

        $validator = Validator::make($request->all(), [
            'fecha_hora' => 'sometimes|required|date|after_or_equal:now',
            'estado' => 'sometimes|required|in:programada,cancelada',
            'motivo_consulta' => 'sometimes|required|string|max:1000',
            'observaciones' => 'nullable|string|max:1000',
            'medico_id' => 'sometimes|required|exists:medicos,id'
        ]);

        if ($validator->fails()) { return response()->json(['errors' => $validator->errors()], 422); }
        
        $cita->update($validator->validated());
        return response()->json($cita->load(['medico']));
    }

    public function eliminarMiCita(Request $request, string $id)
    {
        $user = $request->user();
        $paciente = Paciente::where('email', $user->email)->first();
        if (!$paciente) { return response()->json(['message' => 'No se encontró un paciente asociado.'], 403); }

        $cita = Cita::where('id', $id)->where('paciente_id', $paciente->id)->first();
        if (!$cita) { return response()->json(['message' => 'Cita no encontrada o no pertenece a este paciente.'], 404); }

        $cita->delete();
        return response()->json(['message' => 'Cita eliminada exitosamente']);
    }
}


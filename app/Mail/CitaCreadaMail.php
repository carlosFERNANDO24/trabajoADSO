<?php

namespace App\Mail;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Para registrar errores de formateo

class CitaCreadaMail extends Mailable // Opcional: implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Cita $cita; // <<< Type hint añadido
    public Paciente $paciente; // <<< Type hint añadido

    /**
     * Create a new message instance.
     */
    public function __construct(Cita $cita, Paciente $paciente)
    {
        $this->cita = $cita;
        $this->paciente = $paciente;
        // Cargamos la relación médico para asegurarnos que esté disponible
        $this->cita->loadMissing('medico');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Formateamos la fecha en español
        try {
            // Aseguramos que Carbon use español
            Carbon::setLocale('es');
            $fechaHoraFormateada = Carbon::parse($this->cita->fecha_hora)
                ->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] h:mm A');
        } catch (\Exception $e) {
            // Fallback si la fecha es inválida
            $fechaHoraFormateada = $this->cita->fecha_hora instanceof \DateTimeInterface
                                    ? $this->cita->fecha_hora->format('Y-m-d H:i:s')
                                    : (string) $this->cita->fecha_hora;
             Log::error("Error formateando fecha para CitaCreadaMail (Cita ID: {$this->cita->id}): ".$e->getMessage());
        }

        // Manejo seguro de datos nulos
        $nombrePaciente = trim(($this->paciente->nombre ?? '') . ' ' . ($this->paciente->apellido ?? ''));
        $nombreMedico = trim((optional($this->cita->medico)->nombre ?? '') . ' ' . (optional($this->cita->medico)->apellido ?? ''));
        $especialidadMedico = optional($this->cita->medico)->especialidad ?? 'No especificada';

       return $this->subject('Confirmación de Cita Médica')
            ->markdown('emails.citas.creada')
            ->with([
                'nombrePaciente' => $nombrePaciente ?: 'Estimado(a) Paciente',
                'fechaHoraCita' => $fechaHoraFormateada,
                'nombreMedico' => $nombreMedico ?: 'Médico no asignado',
                'especialidadMedico' => $especialidadMedico,
                'motivoConsulta' => $this->cita->motivo_consulta ?? 'No especificado',
                'observaciones' => $this->cita->observaciones ?? 'Ninguna',
                'appName' => config('app.name'),
            ]);

    }
}


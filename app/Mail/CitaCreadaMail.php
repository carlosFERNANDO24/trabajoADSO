<?php

namespace App\Mail;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CitaCreadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cita;
    public $paciente;

    /**
     * Create a new message instance.
     *
     * @param Cita $cita Modelo Cita con relaciones 'paciente' y 'medico' cargadas.
     * @param Paciente $paciente Modelo Paciente.
     */
    public function __construct(Cita $cita, Paciente $paciente)
    {
        $this->cita = $cita;
        $this->paciente = $paciente;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Cita Médica - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Aseguramos que las relaciones necesarias estén cargadas
        $this->cita->loadMissing(['medico']);

        // Formateamos la fecha y hora para que sea fácil de leer
        $fechaHoraFormateada = Carbon::parse($this->cita->fecha_hora)
            ->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] h:mm A');

        return new Content(
            // Cambiado a 'view' para usar una plantilla HTML simple
            view: 'emails.citas.creada',
            with: [
                'nombrePaciente' => $this->paciente->nombre . ' ' . $this->paciente->apellido,
                'fechaHoraCita' => $fechaHoraFormateada,
                'nombreMedico' => $this->cita->medico ? ($this->cita->medico->nombre . ' ' . $this->cita->medico->apellido) : 'Médico no especificado',
                'especialidadMedico' => $this->cita->medico ? $this->cita->medico->especialidad : 'Especialidad no especificada',
                'motivoConsulta' => $this->cita->motivo_consulta,
                'observaciones' => $this->cita->observaciones ?: 'Ninguna',
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

                    
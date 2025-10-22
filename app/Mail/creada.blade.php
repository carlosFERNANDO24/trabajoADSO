@component('mail::message')
# ¡Confirmación de tu cita médica! 🩺

Hola **{{ $nombrePaciente }}**,  
tu cita ha sido registrada exitosamente en **{{ $appName }}**.

---

### 🕒 Detalles de la cita:

- **Fecha y hora:** {{ ucfirst($fechaHoraCita) }}
- **Médico:** {{ $nombreMedico }}
- **Especialidad:** {{ $especialidadMedico }}
- **Motivo de la consulta:** {{ $motivoConsulta }}
- **Observaciones:** {{ $observaciones }}

---

@component('mail::button', ['url' => config('app.url')])
Ver más detalles
@endcomponent

Gracias por confiar en nosotros,  
**{{ $appName }}**

@endcomponent

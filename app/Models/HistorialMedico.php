<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialMedico extends Model
{
    protected $table = 'historial_medico';
    
    protected $fillable = [
        'diagnostico',
        'tratamiento',
        'notas',
        'fecha_consulta',
        'cita_id',
        'paciente_id',
        'medico_id'
    ];

    protected $casts = [
        'fecha_consulta' => 'date'
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }
}
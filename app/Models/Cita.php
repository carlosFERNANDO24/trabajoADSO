<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cita extends Model
{
    protected $table = 'citas';
    
    protected $fillable = [
        'fecha_hora',
        'estado',
        'motivo_consulta',
        'observaciones',
        'paciente_id',
        'medico_id'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function historialMedico(): HasOne
    {
        return $this->hasOne(HistorialMedico::class, 'cita_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    protected $table = 'pacientes';
    
    protected $fillable = [
        'documento',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date'
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    public function historialesMedicos(): HasMany
    {
        return $this->hasMany(HistorialMedico::class, 'paciente_id');
    }
}
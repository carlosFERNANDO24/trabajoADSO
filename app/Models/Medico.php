<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $table = 'medicos';
    
    protected $fillable = [
        'documento',
        'nombre',
        'apellido',
        'especialidad',
        'telefono',
        'email'
    ];

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'medico_id');
    }

    public function historialesMedicos(): HasMany
    {
        return $this->hasMany(HistorialMedico::class, 'medico_id');
    }
}
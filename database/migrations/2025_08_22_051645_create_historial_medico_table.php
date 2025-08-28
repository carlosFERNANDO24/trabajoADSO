<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_medico', function (Blueprint $table) {
            $table->id();
            $table->text("diagnostico");
            $table->text("tratamiento");
            $table->text("notas")->nullable();
            $table->date("fecha_consulta");
            $table->foreignId("cita_id")->constrained("citas")->onDelete("cascade");
            $table->foreignId("paciente_id")->constrained("pacientes")->onDelete("cascade");
            $table->foreignId("medico_id")->constrained("medicos")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_medico');
    }
};
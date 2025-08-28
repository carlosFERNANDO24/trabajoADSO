<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->dateTime("fecha_hora");
            $table->enum("estado", ["programada", "completada", "cancelada"])->default("programada");
            $table->text("motivo_consulta");
            $table->text("observaciones")->nullable();
            $table->foreignId("paciente_id")->constrained("pacientes")->onDelete("cascade");
            $table->foreignId("medico_id")->constrained("medicos")->onDelete("cascade");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
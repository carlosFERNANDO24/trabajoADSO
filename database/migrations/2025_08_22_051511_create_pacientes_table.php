<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string("documento")->unique();
            $table->string("nombre");
            $table->string("apellido");
            $table->string("telefono")->nullable();
            $table->string("email")->nullable();
            $table->date("fecha_nacimiento")->nullable();
            $table->enum("genero", ["M", "F"]);
            $table->text("direccion")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
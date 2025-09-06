<?php

use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\HistorialMedicoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 🔹 Autenticación
Route::post('/registro', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// 🔹 Rutas protegidas por rol
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Rutas para Pacientes (solo admin)
    Route::get('ListarPacientes', [PacienteController::class, 'index']);
    Route::post('CrearPacientes', [PacienteController::class, 'store']);
    Route::get('MostrarPacientes/{id}', [PacienteController::class, 'show']);
    Route::put('ActualizarPacientes/{id}', [PacienteController::class, 'update']);
    Route::delete('EliminarPacientes/{id}', [PacienteController::class, 'destroy']);

    // Rutas para Médicos (solo admin)
    Route::get('ListarMedicos', [MedicoController::class, 'index']);
    Route::post('CrearMedicos', [MedicoController::class, 'store']);
    Route::get('MostrarMedicos/{id}', [MedicoController::class, 'show']);
    Route::put('ActualizarMedicos/{id}', [MedicoController::class, 'update']);
    Route::delete('EliminarMedicos/{id}', [MedicoController::class, 'destroy']);
});

// 🔹 Rutas accesibles por admin y doctor
Route::middleware(['auth:sanctum', 'role:admin,doctor'])->group(function () {
    // Rutas para Citas
    Route::get('ListarCitas', [CitaController::class, 'index']);
    Route::post('CrearCitas', [CitaController::class, 'store']);
    Route::get('MostrarCitas/{id}', [CitaController::class, 'show']);
    Route::put('ActualizarCitas/{id}', [CitaController::class, 'update']);
    Route::delete('EliminarCitas/{id}', [CitaController::class, 'destroy']);
});

// 🔹 Rutas accesibles por doctor y paciente
Route::middleware(['auth:sanctum', 'role:doctor,paciente'])->group(function () {
    // Rutas para Historial Médico
    Route::get('ListarHistorialMedico', [HistorialMedicoController::class, 'index']);
    Route::post('CrearHistorialMedico', [HistorialMedicoController::class, 'store']);
    Route::get('MostrarHistorialMedico/{id}', [HistorialMedicoController::class, 'show']);
    Route::put('ActualizarHistorialMedico/{id}', [HistorialMedicoController::class, 'update']);
    Route::delete('EliminarHistorialMedico/{id}', [HistorialMedicoController::class, 'destroy']);
});

// 🔹 Rutas adicionales con roles combinados
Route::middleware(['auth:sanctum', 'role:admin,doctor'])->group(function () {
    Route::get('Pacientes/{id}/Citas', [PacienteController::class, 'citasPorPaciente']);
    Route::get('Medicos/{id}/Citas', [MedicoController::class, 'citasPorMedico']);
    Route::get('Citas/{id}/Historial', [CitaController::class, 'historialPorCita']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('pacientes/mayores-60', [PacienteController::class, 'pacientesMayores60']);
    Route::get('medicos/especialidad/{especialidad}', [MedicoController::class, 'medicosPorEspecialidad']);
});

Route::middleware(['auth:sanctum', 'role:doctor,paciente'])->group(function () {
    Route::get('historial/paciente/{pacienteId}', [HistorialMedicoController::class, 'historialPorPaciente']);
});

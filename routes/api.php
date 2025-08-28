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


Route::post('/registro', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});


// Rutas para Pacientes
route::get('ListarPacientes', [PacienteController::class, 'index']);
route::post('CrearPacientes', [PacienteController::class, 'store']);
route::get('MostrarPacientes/{id}', [PacienteController::class, 'show']);
route::put('ActualizarPacientes/{id}', [PacienteController::class, 'update']);
route::delete('EliminarPacientes/{id}', [PacienteController::class, 'destroy']);

// Rutas para Médicos
route::get('ListarMedicos', [MedicoController::class, 'index']);
route::post('CrearMedicos', [MedicoController::class, 'store']);
route::get('MostrarMedicos/{id}', [MedicoController::class, 'show']);
route::put('ActualizarMedicos/{id}', [MedicoController::class, 'update']);
route::delete('EliminarMedicos/{id}', [MedicoController::class, 'destroy']);

// Rutas para Citas
route::get('ListarCitas', [CitaController::class, 'index']);
route::post('CrearCitas', [CitaController::class, 'store']);
route::get('MostrarCitas/{id}', [CitaController::class, 'show']);
route::put('ActualizarCitas/{id}', [CitaController::class, 'update']);
route::delete('EliminarCitas/{id}', [CitaController::class, 'destroy']);

// Rutas para Historial Médico
route::get('ListarHistorialMedico', [HistorialMedicoController::class, 'index']);
route::post('CrearHistorialMedico', [HistorialMedicoController::class, 'store']);
route::get('MostrarHistorialMedico/{id}', [HistorialMedicoController::class, 'show']);
route::put('ActualizarHistorialMedico/{id}', [HistorialMedicoController::class, 'update']);
route::delete('EliminarHistorialMedico/{id}', [HistorialMedicoController::class, 'destroy']);

// Rutas adicionales
route::get('Pacientes/{id}/Citas', [PacienteController::class, 'citasPorPaciente']);
route::get('Medicos/{id}/Citas', [MedicoController::class, 'citasPorMedico']);
route::get('Citas/{id}/Historial', [CitaController::class, 'historialPorCita']);
route::get('pacientes/mayores-60', [PacienteController::class, 'pacientesMayores60']);
route::get('medicos/especialidad/{especialidad}', [MedicoController::class, 'medicosPorEspecialidad']);
route::get('historial/paciente/{pacienteId}', [HistorialMedicoController::class, 'historialPorPaciente']);


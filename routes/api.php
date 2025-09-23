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

/*
|--------------------------------------------------------------------------
| 🔹 Admin: Acceso TOTAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Pacientes
    Route::get('ListarPacientes', [PacienteController::class, 'index']);
    Route::post('CrearPacientes', [PacienteController::class, 'store']);
    Route::get('MostrarPacientes/{id}', [PacienteController::class, 'show']);
    Route::put('ActualizarPacientes/{id}', [PacienteController::class, 'update']);
    Route::delete('EliminarPacientes/{id}', [PacienteController::class, 'destroy']);

    // Médicos
    Route::get('ListarMedicos', [MedicoController::class, 'index']);
    Route::post('CrearMedicos', [MedicoController::class, 'store']);
    Route::get('MostrarMedicos/{id}', [MedicoController::class, 'show']);
    Route::put('ActualizarMedicos/{id}', [MedicoController::class, 'update']);
    Route::delete('EliminarMedicos/{id}', [MedicoController::class, 'destroy']);

    // Citas
    Route::get('ListarCitas', [CitaController::class, 'index']);
    Route::post('CrearCitas', [CitaController::class, 'store']);
    Route::get('MostrarCitas/{id}', [CitaController::class, 'show']);
    Route::put('ActualizarCitas/{id}', [CitaController::class, 'update']);
    Route::delete('EliminarCitas/{id}', [CitaController::class, 'destroy']);

    // Historial Médico
    Route::get('ListarHistorialMedico', [HistorialMedicoController::class, 'index']);
    Route::post('CrearHistorialMedico', [HistorialMedicoController::class, 'store']);
    Route::get('MostrarHistorialMedico/{id}', [HistorialMedicoController::class, 'show']);
    Route::put('ActualizarHistorialMedico/{id}', [HistorialMedicoController::class, 'update']);
    Route::delete('EliminarHistorialMedico/{id}', [HistorialMedicoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| 🔹 Doctor: Citas + Historial (sin CRUD de Pacientes/Médicos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:doctor'])->group(function () {
    // Citas
    Route::get('ListarCitas', [CitaController::class, 'index']);
    Route::post('CrearCitas', [CitaController::class, 'store']);
    Route::get('MostrarCitas/{id}', [CitaController::class, 'show']);
    Route::put('ActualizarCitas/{id}', [CitaController::class, 'update']);
    Route::delete('EliminarCitas/{id}', [CitaController::class, 'destroy']);

    // Historial Médico
    Route::get('ListarHistorialMedico', [HistorialMedicoController::class, 'index']);
    Route::post('CrearHistorialMedico', [HistorialMedicoController::class, 'store']);
    Route::get('MostrarHistorialMedico/{id}', [HistorialMedicoController::class, 'show']);
    Route::put('ActualizarHistorialMedico/{id}', [HistorialMedicoController::class, 'update']);
    Route::delete('EliminarHistorialMedico/{id}', [HistorialMedicoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| 🔹 Paciente: Crear cita + Ver mis citas + Ver mi historial
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:paciente'])->group(function () {
    // Solo crear cita
    Route::post('CrearCitas', [CitaController::class, 'store']);

    // Ver solo MIS citas
    Route::get('MisCitas', [CitaController::class, 'misCitas']);

    // Ver solo MI historial
    Route::get('MiHistorialMedico', [HistorialMedicoController::class, 'miHistorial']);
});

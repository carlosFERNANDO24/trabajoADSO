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
// Rutas exclusivas para el administrador
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Médicos (CRUD)
    Route::post('CrearMedicos', [MedicoController::class, 'store']);
    Route::put('ActualizarMedicos/{id}', [MedicoController::class, 'update']);
    Route::delete('EliminarMedicos/{id}', [MedicoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| 🔹 Admin & Doctor: Acceso compartido
|--------------------------------------------------------------------------
*/
// Rutas compartidas entre administradores y doctores
Route::middleware(['auth:sanctum', 'role:admin,doctor'])->group(function () {
    // Rutas de Pacientes (CRUD para admin/doctor)
    Route::post('CrearPacientes', [PacienteController::class, 'store']);
    Route::get('ListarPacientes', [PacienteController::class, 'index']);
    Route::get('MostrarPacientes/{id}', [PacienteController::class, 'show']);

    // Citas (CRUD)
    Route::get('ListarCitas', [CitaController::class, 'index']);
    Route::post('CrearCitas', [CitaController::class, 'store']);
    Route::get('MostrarCitas/{id}', [CitaController::class, 'show']);
    Route::put('ActualizarCitas/{id}', [CitaController::class, 'update']);
    Route::delete('EliminarCitas/{id}', [CitaController::class, 'destroy']);

    // Historial Médico (CRUD)
    Route::get('ListarHistorialMedico', [HistorialMedicoController::class, 'index']);
    Route::post('CrearHistorialMedico', [HistorialMedicoController::class, 'store']);
    Route::get('MostrarHistorialMedico/{id}', [HistorialMedicoController::class, 'show']);
    Route::put('ActualizarHistorialMedico/{id}', [HistorialMedicoController::class, 'update']);
    Route::delete('EliminarHistorialMedico/{id}', [HistorialMedicoController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| 🔹 Admin, Doctor & Paciente: Acceso de solo lectura a médicos
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin,doctor,paciente'])->group(function () {
    // Rutas de solo lectura para Médicos
    Route::get('ListarMedicos', [MedicoController::class, 'index']);
    Route::get('MostrarMedicos/{id}', [MedicoController::class, 'show']);
});


/*
|--------------------------------------------------------------------------
| 🔹 Paciente: Ver mis citas + Ver mi historial
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:paciente'])->group(function () {
    // Ver solo MIS citas
    Route::get('MisCitas', [CitaController::class, 'misCitas']);
    // Ver solo MI historial
    Route::get('MiHistorialMedico', [HistorialMedicoController::class, 'miHistorial']);

    // ✅ Nuevas rutas para pacientes
    Route::post('CrearMiCita', [CitaController::class, 'crearMiCita']);
    Route::put('ActualizarMiCita/{id}', [CitaController::class, 'update']);
    Route::delete('EliminarMiCita/{id}', [CitaController::class, 'destroy']);
    
    // ✅ NUEVO: Ruta para que el paciente cree su propio perfil
    Route::post('CrearMiPaciente', [PacienteController::class, 'storeMiPaciente']);
});
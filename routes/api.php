<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PoliController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\BookingController;
use App\Models\Patient;
use Illuminate\Http\Request;

Route::get('/polis', [PoliController::class, 'index']);

Route::get('/doctors', [DoctorController::class, 'index']);

Route::get('/schedules', [ScheduleController::class, 'index']);

Route::post('/booking', [BookingController::class, 'store']);

Route::get('/booking/{id}', [BookingController::class, 'show']);

Route::get('/queues', [BookingController::class, 'queues']);

Route::post('/call-queue/{id}', [BookingController::class, 'callQueue']);

// Endpoint untuk menyimpan data pasien otomatis setelah register Firebase
Route::post('/patients', function (Request $request) {
    $request->validate([
        'id' => 'required|string', // UID Firebase
        'nama' => 'required|string',
        'nik' => 'required|string|unique:patients,nik',
    ]);

    $patient = Patient::create([
        'id' => $request->id,
        'nama' => $request->nama,
        'nik' => $request->nik,
    ]);

    return response()->json(['success' => true, 'data' => $patient]);
});
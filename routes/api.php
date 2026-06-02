<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PoliController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\ScheduleController;
use App\Http\Controllers\API\BookingController;

Route::get('/polis', [PoliController::class, 'index']);

Route::get('/doctors', [DoctorController::class, 'index']);

Route::get('/schedules', [ScheduleController::class, 'index']);

Route::post('/booking', [BookingController::class, 'store']);

Route::get('/booking/{id}', [BookingController::class, 'show']);
Route::patch('/booking/{id}', [BookingController::class, 'updateStatus']);

Route::get('/queues', [BookingController::class, 'queues']);

Route::post('/call-queue/{id}', [BookingController::class, 'callQueue']);

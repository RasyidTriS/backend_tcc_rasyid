<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        return response()->json(
            Schedule::with(['doctor', 'poli'])->get()
        );
    }
}
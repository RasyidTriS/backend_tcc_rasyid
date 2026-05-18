<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index()
    {
        return response()->json(
            Doctor::all()
        );
    }
}
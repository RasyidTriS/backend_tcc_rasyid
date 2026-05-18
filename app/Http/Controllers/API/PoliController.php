<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Poli;

class PoliController extends Controller
{
    public function index()
    {
        return response()->json(
            Poli::all()
        );
    }
}
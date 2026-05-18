<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Poli;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'schedule_id' => 'required|integer|exists:schedules,id',
            'tanggal' => 'required|date'
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        $poli = Poli::findOrFail($schedule->poli_id);

        $lastBooking = Booking::whereDate('tanggal', $request->tanggal)
            ->whereHas('schedule', function ($q) use ($schedule) {
                $q->where('poli_id', $schedule->poli_id);
            })
            ->latest()
            ->first();

        $nextNumber = 1;

        if ($lastBooking) {
            $lastNumber = intval(substr($lastBooking->nomor_antrean, 1));
            $nextNumber = $lastNumber + 1;
        }

        $nomorAntrean = $poli->kode_poli .
            str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $booking = Booking::create([
            'patient_id' => $request->patient_id,
            'schedule_id' => $request->schedule_id,
            'tanggal' => $request->tanggal,
            'nomor_antrean' => $nomorAntrean,
            'status' => 'waiting'
        ]);

        return response()->json([
            'success' => true,
            'data' => Booking::with([
                'patient',
                'schedule.doctor',
                'schedule.poli'
            ])->find($booking->id)
        ]);
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        return response()->json($booking);
    }

    public function queues()
    {
        $queues = Booking::with([
            'patient',
            'schedule.poli'
        ])
        ->whereDate('tanggal', today())
        ->orderBy('nomor_antrean')
        ->get();

        return response()->json($queues);
    }

    public function callQueue($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->status = 'calling';
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Queue called',
            'data' => $booking
        ]);
    }
}

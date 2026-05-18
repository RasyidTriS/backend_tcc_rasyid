<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'poli_id',
        'hari',
        'jam_mulai',
        'jam_selesai'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }   
}
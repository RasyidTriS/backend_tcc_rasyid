<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\Schedule;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat 1 Pasien Dummy
        Patient::create([
            'nama' => 'Budi Santoso',
            'nik' => '1234567890123456',
            'no_hp' => '08123456789',
            'alamat' => 'Jl. Merdeka No. 1, Yogyakarta'
        ]);

        // 2. Buat 1 Poli Dummy
        $poli = Poli::create([
            'nama_poli' => 'Poli Umum',
            'kode_poli' => 'UMM'
        ]);

        // 3. Buat 1 Dokter Dummy
        $doctor = Doctor::create([
            'nama' => 'dr. Andi Wijaya',
            'spesialis' => 'Umum',
            'no_hp' => '08987654321'
        ]);

        // 4. Buat 1 Jadwal Dummy (Menghubungkan Dokter dan Poli)
        Schedule::create([
            'doctor_id' => $doctor->id,
            'poli_id' => $poli->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00'
        ]);
    }
}
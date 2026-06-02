<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Tambahkan baris ini agar semua kolom (termasuk ID) diizinkan untuk diisi
    protected $guarded = [];
}

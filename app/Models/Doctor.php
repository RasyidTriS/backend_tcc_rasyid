<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}

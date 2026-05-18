<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}

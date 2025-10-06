<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    public function mfDailyStats()
    {
        return $this->hasMany(Mf_Daily_Stats::class);
    }
}

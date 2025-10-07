<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    protected $fillable = ['name'];
    protected $table = 'amcs';

    public function mutualFunds()
    {
        return $this->hasMany(MutualFunds::class, 'amc_id');
    }

    public function mfDailyStats()
    {
        return $this->hasMany(Mf_Daily_Stats::class, 'amc_id');
    }
}

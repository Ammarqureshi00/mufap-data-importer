<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    protected $fillable = ['name'];
    protected $table = 'amcs';

    public function mutualFunds()
    {
        return $this->hasMany(MutualFund::class, 'amc_id');
    }

    public function mfDailyStats()
    {
        return $this->hasMany(MfDailyStat::class, 'amc_id');
    }
}

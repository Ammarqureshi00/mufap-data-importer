<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutualFunds extends Model
{
    protected $fillable = ['name', 'amc_id'];
    protected $table = 'mutual_funds';

    public function amc()
    {
        return $this->belongsTo(Amc::class);
    }

    // public function dailyStats()
    // {
    //     return $this->hasMany(Mf_Daily_Stats::class);
    // }
}

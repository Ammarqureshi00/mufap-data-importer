<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    protected $fillable = ['name', 'created_at'];

    // public function mutualFunds()
    // {
    //     return $this->hasMany(MutualFunds::class);
    // }

    // public function dailyStats()
    // {
    //     return $this->hasMany(Mf_Daily_Stats::class);
    // }
}

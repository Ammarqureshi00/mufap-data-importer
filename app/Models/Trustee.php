<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trustee extends Model
{
    protected $fillable = ['name'];

    // public function dailyStats()
    // {
    //     return $this->hasMany(Mf_Daily_Stats::class);
    // }
}

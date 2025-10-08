<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $fillable = ['name'];
    protected $table = 'sectors';

    public function mfDailyStats()
    {
        return $this->hasMany(MfDailyStat::class, 'sector_id');
    }
}

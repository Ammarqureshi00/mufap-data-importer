<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trustee extends Model
{
    protected $fillable = ['name'];
    protected $table = 'trustees';

    // public function dailyStats()
    // {
    //     return $this->hasMany(MfDailyStat::class);
    // }
}

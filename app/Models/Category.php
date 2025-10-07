<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $table = 'categories';

    public function mfDailyStats()
    {
        return $this->hasMany(Mf_Daily_Stats::class, 'category_id');
    }

    public function mutualFunds()
    {
        return $this->hasMany(MutualFunds::class, 'category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];
    protected $table = 'categories';

    public function mfDailyStats()
    {
        return $this->hasMany(MfDailyStat::class, 'category_id');
    }

    public function mutualFunds()
    {
        return $this->hasMany(MutualFund::class, 'category_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mf_Daily_Stats extends Model
{
    use HasFactory;

    protected $table = 'mf_daily_stats';

    protected $fillable = [
        'mutual_fund_id',
        'amc_id',
        'sector_id',
        'trustee_id',
        'category_id',
        'inception_date',
        'offer',
        'repurchase',
        'nav',
        'validity_date',
        'front_end',
        'back_end',
        'contingent',
        'market',
    ];

    protected $casts = [
        'validity_date'  => 'date:Y-m-d',    // will be serialized as "2025-10-01"
        'inception_date' => 'date:Y-m-d',
        'offer'          => 'decimal:4',
        'repurchase'     => 'decimal:4',
        'nav'            => 'decimal:4',
        'front_end'      => 'decimal:4',
        'back_end'       => 'decimal:4',
        'contingent'     => 'decimal:4',
        'market'         => 'decimal:4',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    public function mutualFund()
    {
        return $this->belongsTo(MutualFunds::class, 'mutual_fund_id', 'id');
    }

    public function amc()
    {
        return $this->belongsTo(Amc::class, 'amc_id', 'id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id', 'id');
    }

    public function trustee()
    {
        return $this->belongsTo(Trustee::class, 'trustee_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // -----------------------
    // Query Scopes
    // -----------------------

    public function scopeByDate($query, $date)
    {
        return $query->where('validity_date', $date);
    }

    public function scopeByAmc($query, $amcId)
    {
        return $query->where('amc_id', $amcId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category_id', $category);
    }

    public function scopeBySector($query, $sectorId)
    {
        return $query->where('sector_id', $sectorId);
    }

    public function scopeByFund($query, $fundId)
    {
        return $query->where('mutual_fund_id', $fundId);
    }

    // public function scopeByTrustee($query, $trusteeId)
    // {
    //     return $query->where('trustee_id', $trusteeId);
    // }
}

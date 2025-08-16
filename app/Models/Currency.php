<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'conversion_rate',
        'is_default',
        'is_active'
    ];

    /**
     * Convert amount from this currency to BDT
     *
     * @param float $amount
     * @return float
     */
    public function convertToBDT($amount)
    {
        return $amount * $this->conversion_rate;
    }

    /**
     * Convert amount from BDT to this currency
     *
     * @param float $amount
     * @return float
     */
    public function convertFromBDT($amount)
    {
        return $amount / $this->conversion_rate;
    }
}

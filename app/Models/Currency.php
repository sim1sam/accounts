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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'conversion_rate' => 'decimal:5',
    ];
    
    /**
     * Get the is_default attribute
     *
     * @param mixed $value
     * @return bool
     */
    public function getIsDefaultAttribute($value)
    {
        return (bool) $value;
    }
    
    /**
     * Get the is_active attribute
     *
     * @param mixed $value
     * @return bool
     */
    public function getIsActiveAttribute($value)
    {
        return (bool) $value;
    }

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

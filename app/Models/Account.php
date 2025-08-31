<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'category',
        'initial_amount',
        'current_amount',
        'currency_id',
        'is_active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
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
     * Get the currency associated with the account.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the amount in BDT
     *
     * @return float
     */
    public function getAmountInBDT()
    {
        if ($this->currency->code === 'BDT') {
            return $this->current_amount;
        }
        
        return $this->current_amount * $this->currency->conversion_rate;
    }

    /**
     * Increase the account balance
     *
     * @param float $amount
     * @return bool
     */
    public function increaseBalance($amount)
    {
        $this->current_amount += $amount;
        return $this->save();
    }

    /**
     * Decrease the account balance
     *
     * @param float $amount
     * @return bool
     */
    public function decreaseBalance($amount)
    {
        if ($this->current_amount >= $amount) {
            $this->current_amount -= $amount;
            return $this->save();
        }
        return false;
    }
}

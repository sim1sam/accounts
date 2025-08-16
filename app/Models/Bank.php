<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'account_name',
        'account_number',
        'branch',
        'currency_id',
        'initial_balance',
        'current_balance',
        'amount_in_bdt',
        'is_active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'amount_in_bdt' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the currency associated with the bank.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
    
    /**
     * Increase the bank balance
     *
     * @param float $amount
     * @param bool $isBDT Whether the amount is in BDT or bank's currency
     * @return bool
     */
    public function increaseBalance($amount, $isBDT = true)
    {
        if ($isBDT) {
            $this->amount_in_bdt += $amount;
            if ($this->currency) {
                $this->current_balance += $amount / $this->currency->conversion_rate;
            } else {
                $this->current_balance += $amount;
            }
        } else {
            $this->current_balance += $amount;
            if ($this->currency) {
                $this->amount_in_bdt += $amount * $this->currency->conversion_rate;
            } else {
                $this->amount_in_bdt += $amount;
            }
        }
        return $this->save();
    }
    
    /**
     * Decrease the bank balance
     *
     * @param float $amount
     * @param bool $isBDT Whether the amount is in BDT or bank's currency
     * @return bool
     */
    public function decreaseBalance($amount, $isBDT = true)
    {
        if ($isBDT) {
            if ($this->amount_in_bdt >= $amount) {
                $this->amount_in_bdt -= $amount;
                if ($this->currency) {
                    $this->current_balance -= $amount / $this->currency->conversion_rate;
                } else {
                    $this->current_balance -= $amount;
                }
                return $this->save();
            }
        } else {
            if ($this->current_balance >= $amount) {
                $this->current_balance -= $amount;
                if ($this->currency) {
                    $this->amount_in_bdt -= $amount * $this->currency->conversion_rate;
                } else {
                    $this->amount_in_bdt -= $amount;
                }
                return $this->save();
            }
        }
        return false;
    }
}

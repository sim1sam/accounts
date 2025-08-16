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
        $amount = (float) $amount;
        if ($amount <= 0) {
            return true; // nothing to do
        }
        // Normalize existing balances (treat null as 0)
        $this->amount_in_bdt = (float) ($this->amount_in_bdt ?? 0);
        $this->current_balance = (float) ($this->current_balance ?? 0);

        if ($isBDT) {
            $this->amount_in_bdt += $amount;
            if ($this->currency) {
                $rate = (float) ($this->currency->conversion_rate ?? 1);
                $this->current_balance += $rate > 0 ? ($amount / $rate) : $amount;
            } else {
                $this->current_balance += $amount;
            }
        } else {
            $this->current_balance += $amount;
            if ($this->currency) {
                $rate = (float) ($this->currency->conversion_rate ?? 1);
                $this->amount_in_bdt += $amount * $rate;
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
        $amount = (float) $amount;
        if ($amount <= 0) {
            return true; // nothing to do
        }
        // Normalize existing balances (treat null as 0)
        $currentBdt = (float) ($this->amount_in_bdt ?? 0);
        $currentNative = (float) ($this->current_balance ?? 0);
        $rate = $this->currency ? (float) ($this->currency->conversion_rate ?? 1) : 1.0;
        if ($rate <= 0) { $rate = 1.0; }

        if ($isBDT) {
            // If BDT balance seems uninitialized but native has value, fall back to computed BDT
            if ($currentBdt <= 0 && $currentNative > 0) {
                $currentBdt = $rate > 0 ? ($currentNative * $rate) : $currentNative;
            }
            if ($currentBdt + 1e-6 >= $amount) { // epsilon to avoid float edge cases
                $this->amount_in_bdt = $currentBdt - $amount;
                if ($this->currency) {
                    $this->current_balance = $currentNative - ($amount / $rate);
                } else {
                    $this->current_balance = $currentNative - $amount;
                }
                return $this->save();
            }
        } else {
            if ($currentNative + 1e-6 >= $amount) {
                $this->current_balance = $currentNative - $amount;
                if ($this->currency) {
                    $this->amount_in_bdt = $currentBdt - ($amount * $rate);
                } else {
                    $this->amount_in_bdt = $currentBdt - $amount;
                }
                return $this->save();
            }
        }
        return false;
    }
}

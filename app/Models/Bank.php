<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'name',
        'account_name',
        'account_number',
        'branch',
        'initial_balance',
        'current_balance',
        'is_active'
    ];
    
    /**
     * Increase the bank balance
     *
     * @param float $amount
     * @return bool
     */
    public function increaseBalance($amount)
    {
        $this->current_balance += $amount;
        return $this->save();
    }
    
    /**
     * Decrease the bank balance
     *
     * @param float $amount
     * @return bool
     */
    public function decreaseBalance($amount)
    {
        if ($this->current_balance >= $amount) {
            $this->current_balance -= $amount;
            return $this->save();
        }
        return false;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'account_id',
        'currency_id',
        'remarks',
        'amount_in_bdt',
        'status',
        'paid_at',
        'transaction_id'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'amount_in_bdt' => 'decimal:2',
        'paid_at' => 'datetime',
    ];


    /**
     * Get the account associated with the expense.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Currency of the native amount for this expense.
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the main transaction if this expense is paid.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get account transactions for this expense.
     */
    public function accountTransactions()
    {
        return $this->morphMany(AccountTransaction::class, 'reference');
    }

    /**
     * Check if expense is paid.
     */
    public function isPaid()
    {
        return $this->status === 'paid';
    }

    /**
     * Check if expense is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}

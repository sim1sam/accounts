<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'payment_id',
        'bank_id',
        'amount',
        'type',
        'description',
        'transaction_date',
        'reference_type',
        'reference_id',
        'voided_at',
        'void_reason',
    ];
    
    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'voided_at' => 'datetime',
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    /**
     * Get the related reference model (e.g., Payment, Expense).
     */
    public function reference()
    {
        return $this->morphTo('reference');
    }

    public function getIsVoidedAttribute(): bool
    {
        return !is_null($this->voided_at);
    }
}

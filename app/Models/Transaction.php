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
    ];
    
    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}

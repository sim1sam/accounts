<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDailyBalance extends Model
{
    protected $fillable = [
        'bank_id',
        'date',
        'physical_amount',
        'physical_amount_bdt',
        'system_amount_bdt',
        'difference_bdt',
        'note',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'physical_amount' => 'decimal:2',
        'physical_amount_bdt' => 'decimal:2',
        'system_amount_bdt' => 'decimal:2',
        'difference_bdt' => 'decimal:2',
    ];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

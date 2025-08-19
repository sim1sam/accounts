<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'customer_id',
        'cancellation_id',
        'bank_id',
        'refund_amount',
        'refund_date',
        'remarks'
    ];

    protected $casts = [
        'refund_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function cancellation()
    {
        return $this->belongsTo(Cancellation::class);
    }
}

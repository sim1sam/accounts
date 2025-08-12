<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'customer_id',
        'refund_amount',
        'refund_date',
        'account',
        'remarks'
    ];

    protected $casts = [
        'refund_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

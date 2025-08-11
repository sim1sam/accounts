<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    protected $fillable = [
        'customer_id',
        'cancellation_value',
        'remarks',
        'cancellation_date'
    ];

    protected $casts = [
        'cancellation_date' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

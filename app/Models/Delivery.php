<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'customer_id',
        'delivery_value',
        'delivery_date',
        'shipment_no',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

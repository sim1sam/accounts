<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'customer_id',
        'amount',
        'payment_date',
        'account_no',
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

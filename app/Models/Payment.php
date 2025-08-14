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
        'bank_id',
    ];
    
    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

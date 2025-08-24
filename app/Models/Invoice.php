<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'staff_id',
        'invoice_value',
        'invoice_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    /**
     * Get the customer that owns the invoice.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the staff member associated with the invoice.
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}

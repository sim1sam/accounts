<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'month', // YYYY-MM
        'remarks',
        'status', // planned, converted
    ];

    protected $casts = [
        'month' => 'date:Y-m',
    ];

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }
}

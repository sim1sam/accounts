<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the accounts for this category.
     */
    public function accounts()
    {
        return $this->hasMany(Account::class, 'category', 'name');
    }
}

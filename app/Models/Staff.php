<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'status',
    ];

    /**
     * Get the customers managed by this staff member.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'kam', 'id');
    }
}

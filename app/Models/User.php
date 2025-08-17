<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_protected' => 'boolean',
        ];
    }

    public function menuPermissions()
    {
        return $this->hasMany(MenuPermission::class);
    }

    public function hasMenu(string $key): bool
    {
        if (($this->role ?? 'staff') === 'admin') { return true; }
        return $this->menuPermissions()->where('menu_key', $key)->exists();
    }

    protected static function booted()
    {
        static::deleting(function (User $user) {
            if ($user->is_protected) {
                throw new \RuntimeException('This user is protected and cannot be deleted.');
            }
        });
    }
}

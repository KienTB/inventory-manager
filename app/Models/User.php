<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'photo',
        'name',
        'username',
        'email',
        'password',
        'role',
        'bank_name',
        'bank_bin',
        'bank_account_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'role' => 'user',
    ];

    public function isAdmin(): bool
    {
        return isset($this->role) && $this->role === 'admin';
    }

    public function isUser(): bool
    {
        return !isset($this->role) || $this->role === 'user';
    }

    public function scopeSearch($query, $value): void
    {
        $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%");
    }

    public function getRouteKeyName(): string
    {
        return 'username';
    }
}

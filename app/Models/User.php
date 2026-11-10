<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Cek apakah user memiliki role tertentu (mis. 'penjual', 'pembeli')
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isPenjual(): bool
    {
        return $this->role === 'penjual';
    }

    public function isPembeli(): bool
    {
        return $this->role === 'pembeli';
    }

    // Relasi: pelanggan memiliki satu akun login (user)
    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class);
    }

    // Relasi: User punya banyak transaksi
    public function transaksis()
    {
        return $this->hasMany(TransaksiRental::class, 'user_id');
    }
}

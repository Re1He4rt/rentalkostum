<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'telepon',
        'email',
        'alamat',
    ];

    // Relasi: setiap pelanggan terhubung ke satu akun login (user)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaksiRental(): HasMany
    {
        return $this->hasMany(TransaksiRental::class);
    }
}

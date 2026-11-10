<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransaksiRental extends Model
{
    protected $table = 'transaksi_rental';

    protected $fillable = [
        'kode_transaksi',
        'pelanggan_id',
        'user_id',
        'tanggal_sewa',
        'tanggal_rencana_kembali',
        'tanggal_kembali',
        'total_harga',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sewa' => 'date',
            'tanggal_rencana_kembali' => 'date',
            'tanggal_kembali' => 'date',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }
}

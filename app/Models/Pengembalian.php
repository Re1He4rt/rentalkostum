<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'transaksi_rental_id',
        'tanggal_kembali',
        'kondisi',
        'denda',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kembali' => 'date',
            'denda' => 'decimal:2',
        ];
    }

    public function transaksiRental(): BelongsTo
    {
        return $this->belongsTo(TransaksiRental::class);
    }
}

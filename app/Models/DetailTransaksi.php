<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_rental_id',
        'kostum_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaksiRental(): BelongsTo
    {
        return $this->belongsTo(TransaksiRental::class);
    }

    public function kostum(): BelongsTo
    {
        return $this->belongsTo(Kostum::class);
    }
}

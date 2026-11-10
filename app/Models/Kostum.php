<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kostum extends Model
{
    protected $table = 'kostum';

    protected $fillable = [
        'kategori_id',
        'kode',
        'nama',
        'ukuran',
        'harga_sewa',
        'stok',
        'gambar',
        'deskripsi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_sewa' => 'decimal:2',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}

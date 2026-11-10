<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Kostum;
use Illuminate\Database\Seeder;

class KostumSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = Kategori::pluck('id', 'nama');

        $kostum = [
            ['kode' => 'KST-001', 'nama' => 'Kebaya Jawa', 'kategori' => 'Tradisional', 'ukuran' => 'M', 'harga_sewa' => 75000, 'stok' => 5],
            ['kode' => 'KST-002', 'nama' => 'Baju Bodo', 'kategori' => 'Tradisional', 'ukuran' => 'L', 'harga_sewa' => 85000, 'stok' => 3],
            ['kode' => 'KST-003', 'nama' => 'Gaun Pesta Merah', 'kategori' => 'Modern', 'ukuran' => 'S', 'harga_sewa' => 150000, 'stok' => 2],
            ['kode' => 'KST-004', 'nama' => 'Jas Formal', 'kategori' => 'Modern', 'ukuran' => 'XL', 'harga_sewa' => 120000, 'stok' => 4],
            ['kode' => 'KST-005', 'nama' => 'Kostum Naruto', 'kategori' => 'Cosplay', 'ukuran' => 'M', 'harga_sewa' => 100000, 'stok' => 6],
            ['kode' => 'KST-006', 'nama' => 'Seragam Perawat', 'kategori' => 'Profesi', 'ukuran' => 'M', 'harga_sewa' => 60000, 'stok' => 8],
        ];

        foreach ($kostum as $row) {
            $kategoriId = $kategori[$row['kategori']] ?? null;

            Kostum::updateOrCreate(
                ['kode' => $row['kode']],
                [
                    'nama' => $row['nama'],
                    'kategori_id' => $kategoriId,
                    'ukuran' => $row['ukuran'],
                    'harga_sewa' => $row['harga_sewa'],
                    'stok' => $row['stok'],
                    'status' => 'tersedia',
                ],
            );
        }
    }
}

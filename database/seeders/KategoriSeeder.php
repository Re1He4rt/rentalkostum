<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Tradisional', 'deskripsi' => 'Kostum adat dan tradisional daerah'],
            ['nama' => 'Modern', 'deskripsi' => 'Kostum modern, pesta, dan gaun'],
            ['nama' => 'Cosplay', 'deskripsi' => 'Kostum karakter dan cosplay'],
            ['nama' => 'Profesi', 'deskripsi' => 'Kostum profesi dan kedinasan'],
        ];

        foreach ($kategori as $row) {
            Kategori::updateOrCreate(['nama' => $row['nama']], $row);
        }
    }
}

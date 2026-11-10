<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PelangganSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = [
            ['nama' => 'Budi Santoso', 'nik' => '3273010101990001', 'telepon' => '081234567890', 'email' => 'budi@example.com', 'alamat' => 'Jl. Merdeka No. 10, Bandung'],
            ['nama' => 'Siti Aminah', 'nik' => '3273010202980002', 'telepon' => '081298765432', 'email' => 'siti@example.com', 'alamat' => 'Jl. Sudirman No. 25, Bandung'],
            ['nama' => 'Andi Wijaya', 'nik' => '3273010303970003', 'telepon' => '085711223344', 'email' => 'andi@example.com', 'alamat' => 'Jl. Asia Afrika No. 5, Bandung'],
            ['nama' => 'Dewi Lestari', 'nik' => '3273010404960004', 'telepon' => '082133445566', 'email' => 'dewi@example.com', 'alamat' => 'Jl. Dago No. 88, Bandung'],
        ];

        foreach ($pelanggan as $row) {
            // Buat/perbarui akun login pelanggan (role pembeli) memakai email pelanggan
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama'],
                    'role' => 'pembeli',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            // Simpan data pelanggan sekaligus menghubungkannya ke akun login
            Pelanggan::updateOrCreate(
                ['nik' => $row['nik']],
                $row + ['user_id' => $user->id],
            );
        }
    }
}

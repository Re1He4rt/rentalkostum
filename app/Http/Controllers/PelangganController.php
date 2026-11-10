<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePelangganRequest;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelanggan = Pelanggan::with('user')
            ->withCount('transaksiRental')
            ->when($request->input('q'), function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', "%{$q}%")
                        ->orWhere('nik', 'like', "%{$q}%")
                        ->orWhere('telepon', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(StorePelangganRequest $request)
    {
        $data = $request->validated();

        // Bungkus dalam transaksi database agar pelanggan & akun login selalu konsisten
        DB::transaction(function () use ($data) {
            // Buat akun login otomatis untuk pelanggan (role pembeli)
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => 'password', // cast 'hashed' akan meng-hash otomatis
                'role' => 'pembeli',
                'email_verified_at' => now(),
            ]);

            // Hubungkan pelanggan ke akun login yang baru dibuat
            $data['user_id'] = $user->id;

            Pelanggan::create($data);
        });

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan. Akun login dibuat dengan password awal "password".');
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->loadCount('transaksiRental')->load('user');

        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(StorePelangganRequest $request, Pelanggan $pelanggan)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $pelanggan) {
            $user = $pelanggan->user;

            if ($user) {
                // Perbarui akun login yang sudah terhubung (nama & email)
                $user->update([
                    'name' => $data['nama'],
                    'email' => $data['email'],
                ]);
            } else {
                // Buat akun login bila pelanggan lama belum memilikinya
                $user = User::create([
                    'name' => $data['nama'],
                    'email' => $data['email'],
                    'password' => 'password',
                    'role' => 'pembeli',
                    'email_verified_at' => now(),
                ]);

                $data['user_id'] = $user->id;
            }

            $pelanggan->update($data);
        });

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $user = $pelanggan->user;

        $pelanggan->delete();

        // Hapus akun login terkait, kecuali akun yang sedang dipakai untuk login
        if ($user && $user->id !== Auth::id()) {
            $user->delete();
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
